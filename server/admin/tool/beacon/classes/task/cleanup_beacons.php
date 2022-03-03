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

namespace tool_beacon\task;

/**
 * Task to clean up old beacon data
 *
 * @package   tool_beacon
 * @author    Kevin Pham <kevinpham@catalyst-au.net>
 * @copyright Catalyst IT, 2022
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cleanup_beacons extends \core\task\scheduled_task {
    /**
     * Get task name
     */
    public function get_name() {
        return get_string('taskcleanupbeacons', 'tool_beacon');
    }

    /**
     * Execute task.
     */
    public function execute() {
        global $DB;

        // For now clean up beacons older than 1 week.
        $now = time();
        $lastweek = $now - WEEKSECS;
        $DB->delete_records_select('tool_beacon', 'timeanswered < ?', [$lastweek]);

        mtrace('tool_beacon: old beacons successfully removed');
    }
}
