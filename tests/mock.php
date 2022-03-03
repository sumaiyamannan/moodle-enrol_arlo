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

/**
 * Beacon mock server
 *
 * @package    tool_beacon
 * @copyright  Brendan Heywood <brendan@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// No login check is expected here because this serves to mock the FleetTracker
// service, and is only loading the config to ensure the secretkey is used and
// works as expected.
// @codingStandardsIgnoreLine
require_once('../../../../config.php');

$secretkey = get_config('tool_beacon', 'secretkey');
if ($secretkey !== 'testing') {
    // We want to protect prod environments from an arbitrary
    // error log spam vector.
    header('HTTP/1.0 403 Forbidden');
    echo "During testing secretkey MUST be set to 'testing'";
    exit;
}

// Simulate beacon data coming back, just pipe it to error log.
$post = file_get_contents('php://input');
if (!empty($post)) {
    // @codingStandardsIgnoreLine
    error_log($post);
    exit;
}

header('Content-Type: application/json');

// Max age determines how old answers can be before the answers are calculated
// again and beaconed back. This means if the answer is not old enough the next
// time all the questions are processed, it will be skipped.
$maxage = 5 * MINSECS; // The default max age for questions that don't specify a maxage, is 24 hours.

// Simulate asking for questions.
$questions = <<<EOD
[
    {
        "id" : "registration_query",
        "type" : "registration"
    },
    {
        "id" : "moodle_version_query",
        "type" : "version_host"
    },
    {
        "id" : "performance",
        "maxage" : $maxage,
        "type" : "check",
        "params": {
            "type": "performance"
        }
    },
    {
        "id" : "moodle_schema_alignment",
        "maxage" : $maxage,
        "type" : "schema_alignment"
    },
    {
        "id" : "security",
        "type" : "check",
        "params": {
            "type": "security"
        }
    }
]
EOD;
$digest = hash_hmac('sha256', $questions, 'testing');
header('Digest: sha-256=' . $digest);
echo $questions;

