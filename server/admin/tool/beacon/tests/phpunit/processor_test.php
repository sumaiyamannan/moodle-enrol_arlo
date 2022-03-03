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
/**
 * Tests for processor class.
 *
 * @package     tool_beacon
 * @copyright   2020 Nicholas Hoobin <nicholashoobin@catalyst-au.net>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @group       tool_beacon
 */
class processor_test extends \advanced_testcase {

    public function get_processor() {
        $beaconbaseurl = 'https://beaconendpoint.test.com';
        $secretkey = 'secret-key';
        return new \tool_beacon\processor($beaconbaseurl, $secretkey);
    }

    public function test_validate_response_fails_if_no_digest() {
        $processor = $this->get_processor();

        // We're testing a private method, so we need to setup reflector magic.
        $method = new \ReflectionMethod('\tool_beacon\processor', 'validate_response');
        $method->setAccessible(true); // Allow accessing of private method.

        $requestcontents = "contents";
        $isvalid = $method->invoke($processor, [], $requestcontents);

        $this->assertFalse($isvalid);
    }

    public function test_validate_response_fails_if_wrong_digest() {
        $processor = $this->get_processor();

        // We're testing a private method, so we need to setup reflector magic.
        $method = new \ReflectionMethod('\tool_beacon\processor', 'validate_response');
        $method->setAccessible(true); // Allow accessing of private method.

        $requestcontents = "contents";
        $isvalid = $method->invoke($processor, ['Digest' => 'sha-256=wrong-digest'], $requestcontents);

        $this->assertFalse($isvalid);
    }

    public function test_validate_response_succeeds() {
        $processor = $this->get_processor();
        $secretkey = 'secret-key';

        // We're testing a private method, so we need to setup reflector magic.
        $method = new \ReflectionMethod('\tool_beacon\processor', 'validate_response');
        $method->setAccessible(true); // Allow accessing of private method.

        $requestcontents = "contents";
        $digest = hash_hmac('sha256', $requestcontents, $secretkey);
        $isvalid = $method->invoke($processor, ['Digest' => "sha-256={$digest}"], $requestcontents);

        $this->assertTrue($isvalid);
    }

    public function test_process_data() {
        global $CFG;

        // Set up $CFG to be used for cfg type testing.
        $valuetofind = 'foundme';
        $nestedobject = new \stdClass();
        $nestedobject->added = $valuetofind;
        $CFG->tool_beacon_test = ['value' => ['was' => $nestedobject]];

        $processor = $this->get_processor();

        $questiondata = file_get_contents(__DIR__ . '/sample_data/questions.json');
        $decodedquestions = json_decode($questiondata);

        // We're testing a private method, so we need to setup reflector magic.
        $method = new \ReflectionMethod('\tool_beacon\processor', 'process_data');
        $method->setAccessible(true); // Allow accessing of private method.
        ob_start();
        $method->invoke($processor, $decodedquestions);
        ob_get_clean();

        $this->assertNotEmpty($processor->get_json_data());
        $beacondata = json_decode($processor->get_json_data());

        // TODO: Add tests for other question types.
        foreach ($beacondata->answers as $answer) {
            if ($answer->type === 'cfg') {
                $cfgresponses = $answer;
                break;
            }
        }

        $this->assertEquals(json_encode($CFG->wwwroot), reset($cfgresponses->result->wwwroot));
        $this->assertEquals(json_encode(null), reset($cfgresponses->result->{'value.does.not.exist'}));
        $this->assertEquals(json_encode($valuetofind), $cfgresponses->result->{'tool_beacon_test.value.was.added'}->customkey);

        $this->assertCount(count($decodedquestions), $beacondata->answers);

        // Unset the key from the global $CFG variable.
        unset($CFG->tool_beacon_test);
    }
}
