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
 * CFG question type - returns values stored in $CFG.
 *
 * @package    tool_beacon
 * @author     Kevin Pham <kevinpham@catalyst-au.net>
 * @copyright  Catalyst IT, 2021
 */

namespace tool_beacon\question;

use tool_beacon\model\beacon_row_kv;

class cfg extends question {

    /**
     * Get an item from an array using "dot" notation.
     *
     * @param  array  $array
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    private static function get($array, $key, $default = null) {
        if (is_null($key)) {
            return $array;
        }
        if (array_key_exists($key, $array)) {
            return $array[$key];
        }
        if (strpos($key, '.') === false) {
            return isset($array[$key]) ? $array[$key] : $default;
        }
        foreach (explode('.', $key) as $segment) {
            if (array_key_exists($segment, $array)) {
                $array = $array[$segment];
            } else {
                return $default;
            }
        }
        return $array;
    }

    protected function query() {
        global $CFG;
        $result = [];

        $names = $this->params->names;

        foreach ($names as $id => $key) {
            $cfgasarray = json_decode(json_encode($CFG), true);
            $value = json_encode(self::get($cfgasarray, $key));

            if (!isset($value)) {
                continue;
            }

            $result[] = new beacon_row_kv(
                $this->domain,
                $this->timestamp,
                $this->type,
                $this->questionid,
                $key,
                $id,
                $value
            );
        }

        return $result;
    }
}
