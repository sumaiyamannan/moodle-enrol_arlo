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
 * SQL_MENU question type.
 *
 * @package     tool_beacon
 * @copyright   2020 Nicholas Hoobin <nicholashoobin@catalyst-au.net>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_beacon\question;

use tool_beacon\model\beacon_row_kv;

class sql_menu extends question {
    use sql_question_trait;

    protected function query() {
        global $DB;

        $results = [];
        $query = $this->params->sql;

        // Check if the query is not safe and return the error as the response if so.
        if ($this->contains_blocked_word($query)) {
            $records = [
                'did_not_query' => 'ERROR: Query contains a blocked word so it was not executed.'
            ];
        } else {
            $records = $DB->get_records_sql_menu($query);
        }

        foreach ($records as $key => $value) {
            $results[] = new beacon_row_kv(
                $this->domain,
                $this->timestamp,
                $this->type,
                $this->questionid,
                $query,
                $key,
                $value
            );
        }

        return $results;
    }
}
