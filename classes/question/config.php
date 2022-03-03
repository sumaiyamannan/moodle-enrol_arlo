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
 * CONFIG question type.
 *
 * @package     tool_beacon
 * @copyright   2020 Nicholas Hoobin <nicholashoobin@catalyst-au.net>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_beacon\question;

use tool_beacon\model\beacon_row_kv;

class config extends question {

    protected function query() {
        $result = [];

        $plugin = $this->params->plugin;
        $names = $this->params->names;

        foreach ($names as $name) {
            $value = get_config($plugin, $name);

            if ($value === false) {
                continue;
            }

            $result[] = new beacon_row_kv(
                $this->domain,
                $this->timestamp,
                $this->type,
                $this->questionid,
                $plugin,
                $name,
                $value
            );
        }

        return $result;
    }
}
