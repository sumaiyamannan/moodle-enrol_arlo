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
 * SCHEMA_ALIGNMENT question type.
 *
 * @package     tool_beacon
 * @copyright   2021 Tomo Tsuyuki <tomotsuyuki@catalyst-au.net>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_beacon\question;

use tool_beacon\model\beacon_row_kv;

class schema_alignment extends question {

    protected function query() {
        global $DB;
        $results = [];

        $dbmanager = $DB->get_manager();
        $schema = $dbmanager->get_install_xml_schema();

        $errors = $dbmanager->check_database_schema($schema);

        foreach ($errors as $table => $items) {
            $details = [];
            foreach ($items as $item) {
                $details[] = $item;
            }
            $key = 'details';
            $value = implode(PHP_EOL, $details);
            $results[] = new beacon_row_kv(
                $this->domain,
                $this->timestamp,
                $this->type,
                $this->questionid,
                $table,
                $key,
                $value
            );
        }

        return $results;
    }
}
