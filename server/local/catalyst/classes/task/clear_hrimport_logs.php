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
 * Contains class clear_hrimport_logs
 *
 * @package   local_catalyst
 * @copyright 2021 Catalyst IT
 * @author    Sumaiya Javed
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_catalyst\task;

use context_system;

defined('MOODLE_INTERNAL') || die();

/**
 * Clear HRImport logs
 *
 * @package local_catalyst
 */
class clear_hrimport_logs extends \core\task\scheduled_task {

    public function get_name() {
        return get_string('taskclearhrimportlogs', 'local_catalyst');
    }

    public function execute($force = false) {
        global $DB;
        $context = context_system::instance();
        $hrimportlifetime = (int)get_config('local_catalyst', 'hrimportlifetime');
        if (empty($hrimportlifetime) || $hrimportlifetime < 0) {
            return;
        }
        $hrimportlifetime = time() - ($hrimportlifetime * 3600 * 24); // Value in days.
        $lifetimep = array($hrimportlifetime);
        $start = time();
        while ($min = $DB->get_field_select("totara_sync_log", "MIN(time)", "time < ?", $lifetimep)) {
            $params = array(min($min + 3600 * 24, $hrimportlifetime));
            $DB->delete_records_select("totara_sync_log", "time < ? ", $params);
            if (time() > $start + 1200) {
                // Do not churn on log deletion for too long each run.
                break;
            }
        }
    }

}
