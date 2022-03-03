<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace tool_beacon;

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir. "/filelib.php");

use tool_beacon\question\question;

/**
 * Processor class to parse and export json.
 *
 * @package     tool_beacon
 * @copyright   2020 Nicholas Hoobin <nicholashoobin@catalyst-au.net>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class processor {

    /** @var array $json Processed structured data. */
    private $json;

    /** @var array $skips List of skipped question ids */
    private $skips;

    /** @var string $secretkey secret key used to sign/verify requests. */
    private $secretkey;

    /** @var \curl $client Moodle's curl class for HTTP requests. */
    private $client;

    /** @var string $beaconbaseurl base url for beacon api. */
    private $beaconbaseurl;

    /**
     * processor constructor.
     *
     * @param string $beaconbaseurl base url for beacon api.
     * @param string $secretkey secret key used to sign/verify requests.
     */
    public function __construct($beaconbaseurl, $secretkey) {
        global $CFG;
        $this->beaconbaseurl = rtrim($beaconbaseurl, '/') . '/';
        $this->client = new \curl();
        $this->client->setHeader([
            'Accept: application/json',
            "Origin: $CFG->wwwroot",
        ]);
        $this->secretkey = $secretkey;
    }

    /**
     * Returns proxy string to use as a storage client param.
     * String format: 'username:password@127.0.0.1:123'.
     *
     * @return string
     */
    public static function get_proxy_string() {
        global $CFG;
        $proxy = '';
        if (!empty($CFG->proxyhost)) {
            $proxy = $CFG->proxyhost;
            if (!empty($CFG->proxyport)) {
                $proxy .= ':'. $CFG->proxyport;
            }
            if (!empty($CFG->proxyuser) && !empty($CFG->proxypassword)) {
                $proxy = $CFG->proxyuser . ':' . $CFG->proxypassword . '@' . $proxy;
            }
        }
        return $proxy;
    }

    /**
     * Validate response headers.
     *
     * @param array $headers the response headers to validate.
     * @param string $requestcontents the request contents.
     *
     * @return bool if response is valid.
     */
    private function validate_response($headers, $requestcontents) {
        // Due to curl's automatic transformation of headers in HTTP2 to
        // lowercase, the headers expected should be lowercased, but this should
        // cover both cases for now.
        $digest = isset($headers['digest']) ? $headers['digest'] : null;
        $digest = $digest ?: (isset($headers['Digest']) ? $headers['Digest'] : null);
        if (empty($digest)) {
            return false;
        }

        $signature = substr($digest, 8); // Chop off the 'sha-256=' bit.
        if ($signature === false) {
            return false;
        }
        $hashedpayload = hash_hmac('sha256', $requestcontents, $this->secretkey);

        if (!hash_equals($signature, $hashedpayload)) {
            return false;
        }

        return true;
    }

    /**
     * Process questions and get answer data.
     *
     * @param array $questions the question objects to process.
     */
    private function process_data($questions) {
        global $DB;

        // Fetches the time any of these questions were last answered.
        list($questionidsqlin, $inparams) = $DB->get_in_or_equal(array_map(function($question){
            return $question->id;
        }, $questions), SQL_PARAMS_NAMED);
        $sql = "SELECT questionid, timeanswered
                FROM {tool_beacon}
                WHERE questionid $questionidsqlin";
        $lasttimeansweredmap = $DB->get_records_sql_menu($sql, $inparams);

        foreach ($questions as $question) {
            $beaconclass = 'tool_beacon\\question\\' . $question->type;

            $maxage = (isset($question->maxage) ? $question->maxage : (24 * HOURSECS));
            // Add maxage handling here, as the question does not care about
            // when it was last run. Skip answering the question if it had
            // already been answered previously, and has not reached its maxage.
            if (isset($lasttimeansweredmap[$question->id]) &&
                // Compares and skips if the question has not aged well enough,
                // defaulting to 24 hours if maxage is not set.
                // Assumption: The id is enough to uniquely identify a question.
                // If the question changes, the id should change also.
                // Psuedocode: if now < timeWhenOld then skip! (timeWhenOld = lastRun + maxAge).
                time() < ($lasttimeansweredmap[$question->id] + $maxage)
            ) {
                $secondstillmaxage = $lasttimeansweredmap[$question->id] + $maxage - time();
                $delta = format_time($secondstillmaxage);
                $showmaxage = format_time($maxage);
                mtrace("-Skipping- question '{$question->id}' of type".
                       "($beaconclass) as it has not reached `maxage` of $showmaxage " .
                       "yet. $delta left ({$secondstillmaxage})");
                $this->skips[] = $question->id;
                continue;
            }

            $starttime = microtime(true);

            if (class_exists($beaconclass)) {
                /** @var question $query */
                $query = new $beaconclass($question);

                // Catch exceptions, but keep answering other questions.
                try {
                    $jsonresult = $query->get_structured_data();

                    $endtime = microtime(true);
                    $deltatime = format_time(round($endtime - $starttime, 3));
                    mtrace("Processing question '{$question->id}' in $deltatime of type ($beaconclass) payload is "
                        . display_size(strlen(json_encode($jsonresult))));
                    if ($jsonresult) {
                        $this->json[] = $jsonresult;
                    }
                } catch (\Exception $e) {
                    mtrace("Issue detected with question '{$question->id}' of type ($beaconclass): "
                        . $e->getMessage());
                } catch (\Throwable $e) {
                    mtrace("Issue detected with question '{$question->id}' of type ($beaconclass): "
                        . $e->getMessage());
                }

            } else {
                mtrace("ERROR invalid question '{$question->id}' of type ($beaconclass)");
            }
        }
    }

    /**
     * Post JSON results to an endpoint.
     *
     * @param string $results the JSON encoded question results to post.
     * @param string $endpoint the endpoint to POST results to.
     *
     * @return bool
     */
    private function submit_answers($results) {
        $success = false;
        try {
            $signature = hash_hmac('sha256', $results, $this->secretkey);
            $this->client->setHeader("Digest: sha-256={$signature}");

            $body = $this->client->post($this->beaconbaseurl, $results);
            if (!empty($this->client->error)) {
                mtrace($this->client->error);
                return false;
            }
            if ($this->client->info['http_code'] !== 200) {
                $response = json_decode($body);
                $errormsg = isset($response->message) ? $response->message : 'Unknown error occurred';
                mtrace("{$this->client->info['http_code']} - {$errormsg}");
            }

            $success = true;
        } catch (\Throwable $exception) {
            mtrace($exception->getMessage());
        }
        return $success;
    }

    /**
     * Get data as a JSON string.
     *
     * @return false|string JSON encoded string of data or false if could not be encoded.
     */
    public function get_json_data() {
        $version = get_config(__NAMESPACE__, 'version');

        // Appends the skip list to the json response blob.
        return json_encode([
            'version' => $version,
            'answers' => $this->json,
            'skips' => $this->skips,
        ]);
    }

    /**
     * Fetches the questions from the beacon API
     *
     * Returns false if there is an issue, otherwise the body of the response.
     *
     * @return bool|string
     */
    public function get_questions() {
        $body = $this->client->get($this->beaconbaseurl . 'moodle/questions');
        if (!empty($this->client->error)) {
            mtrace($this->client->error);
            return false;
        }

        // Check signature is valid.
        $headers = $this->client->getResponse();
        $valid = $this->validate_response($headers, $body);
        if (!$valid) {
            mtrace("Signature not valid, aborting");
            return false;
        }

        // Decode JSON.
        $questions = json_decode($body);
        if (json_last_error() !== JSON_ERROR_NONE) {
            mtrace("Issue decoding json response");
            return false;
        }

        return $questions;
    }

    /**
     * Called from the scheduled task 'signal_beacon'. This should run through the entire process from,
     * 1. Obtaining the questions (validates signature).
     * 2. Process questions and hold answers.
     * 3. Submit answers back to Fleettracker (includes signature).
     *
     * @return bool true on success, false otherwise.
     */
    public function execute() {
        $success = false;
        // Fetch and beacon the answers.
        try {
            // Get questions.
            $questions = $this->get_questions();
            if ($questions === false) {
                return false;
            }

            // Process questions and calculate answers.
            $this->process_data($questions);

            // Submit answers.
            if (!empty($this->json)) {
                $success = $this->submit_answers($this->get_json_data());
            } else {
                $success = true; // No answers available, no answers to beacon, set success = true.
            }

        } catch (\Throwable $exception) {
            mtrace($exception->getMessage());
            $success = false;
        }

        // On successful beacon, store the previous answers, timeanswered,
        // question id, etc for each question answered in the database.
        if ($success) {
            $this->record_beacons();
        }

        return $success;
    }

    /**
     * Records the latest beaconed answers in the database, upserting previously answered questions.
     *
     * Called from the execute() method, after the results have been beaconed
     * successfully.
     * This records 'submitted' answers, and updates their timeanswered based on
     * the timestamp linked with the answer record. This also upserts the
     * records so previously answered questions and their responses can be
     * shown.
     *
     * @return void
     */
    public function record_beacons() {
        global $DB;

        // Skips updating if there is nothing to update.
        if (empty($this->json)) {
            return;
        }

        // Prepares the relevant fields to be updated/inserted in the beacon table.
        // The format for this data is as follows: questionid => { timeanswered } .
        $updates = array_reduce($this->json, function($acc, $answer) {
            $acc[$answer['questionid']] = [
                'questionid' => $answer['questionid'],
                'type' => $answer['type'],
                'timeanswered' => $answer['timestamp'],
                'answer' => json_encode($answer['result']),
            ];
            return $acc;
        }, []);

        // Fetches all existing questions, to determine whether an update or insert is required.
        list($questionidsqlin, $inparams) = $DB->get_in_or_equal(array_keys($updates), SQL_PARAMS_NAMED);
        $sql = "SELECT questionid, id
                FROM {tool_beacon}
                WHERE questionid $questionidsqlin";
        $updateids = array_map(function($id){
            return ['id' => $id];
        }, $DB->get_records_sql_menu($sql, $inparams));

        // Combines the two arrays into a single collection for easier
        // traversal. If slow could just reference $updateids directly when
        // checking for ids.
        $preparedupdates = array_replace_recursive($updates, $updateids);

        // Loops through and updates or inserts based on whether there is an 'id' record associated with it.
        $bulk = count($preparedupdates) > 1;
        foreach ($preparedupdates as $preparedupdate) {
            if (isset($preparedupdate['id'])) {
                $DB->update_record('tool_beacon', $preparedupdate, $bulk);
            } else {
                $DB->insert_record('tool_beacon', $preparedupdate, false, $bulk);
            }
        }
    }

}
