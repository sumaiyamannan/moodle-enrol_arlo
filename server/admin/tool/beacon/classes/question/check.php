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
 * CHECK question type.
 *
 * @package     tool_beacon
 * @author      Kevin Pham <kevinpham@catalyst-au.net>
 * @copyright   Catalyst IT, 2021
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_beacon\question;

use tool_beacon\model\beacon_row_kv;

class check extends question {

    protected function query() {
        $results = [];

        // Check if the check manager class exists before proceeding, and if it
        // does not exist, return an answer that indicates that it is not
        // supported yet (check.status => unknown).
        if (!class_exists(\core\check\manager::class)) {
            $results[] = new beacon_row_kv(
                $this->domain,
                $this->timestamp,
                $this->type,
                $this->questionid,
                'check',
                'status',
                'unknown'
            );
            return $results;
        }

        $checktype = isset($this->params->type) ? $this->params->type : $this->questionid;
        $checks = \core\check\manager::get_checks($checktype);

        foreach ($checks as $check) {

            $checkresult = $check->get_result();
            $checkresults = [
                'name' => $check->get_name(),
                'status' => $checkresult->get_status(),
                'summary' => $checkresult->get_summary()
            ];

            foreach ($checkresults as $key => $value) {
                $results[] = new beacon_row_kv(
                    $this->domain,
                    $this->timestamp,
                    $this->type,
                    $this->questionid,
                    $check->get_ref(),
                    $key,
                    $value
                );
            }
        }

        return $results;
    }
}
