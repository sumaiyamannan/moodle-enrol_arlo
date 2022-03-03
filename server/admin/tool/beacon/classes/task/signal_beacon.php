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
 * Task to transmit the beacon data.
 *
 * @package     tool_beacon
 * @copyright   2020 Nicholas Hoobin <nicholashoobin@catalyst-au.net>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_beacon\task;

use tool_beacon\processor;

/**
 * Task to transmit the beacon data.
 *
 * @package     tool_beacon
 * @copyright   2020 Nicholas Hoobin <nicholashoobin@catalyst-au.net>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class signal_beacon extends \core\task\scheduled_task {

    /**
     * Get task name
     */
    public function get_name() {
        return get_string('pluginname', 'tool_beacon');
    }

    /**
     * Execute task.
     */
    public function execute() {
        $beaconbaseurl = get_config('tool_beacon', 'beaconbaseurl');
        $secretkey = get_config('tool_beacon', 'secretkey');

        if (!$beaconbaseurl) {
            mtrace('tool_beacon: beaconbaseurl not set');
            return;
        }

        if (!$secretkey) {
            mtrace('tool_beacon: secretkey not set');
            return;
        }

        $processor = new processor($beaconbaseurl, $secretkey);
        $success = $processor->execute();

        if ($success) {
            mtrace('tool_beacon: question answers successfully beaconed');
        } else {
            mtrace('tool_beacon: question processing failed.');
        }
    }
}
