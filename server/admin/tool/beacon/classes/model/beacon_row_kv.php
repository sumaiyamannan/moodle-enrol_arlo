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
 * Model for a beacon row.
 *
 * @package     tool_beacon
 * @copyright   2020 Nicholas Hoobin <nicholashoobin@catalyst-au.net>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_beacon\model;

class beacon_row_kv {
    public $domain;
    public $timestamp;
    public $type;
    public $questionid;
    public $id;
    public $key;
    public $value;

    public function __construct($domain, $timestamp, $type, $questionid, $id, $key, $value) {
        $this->domain = $domain;
        $this->timestamp = $timestamp;
        $this->type = $type;
        $this->questionid = $questionid;
        $this->id = $id;
        $this->key = $key;
        $this->value = $value;
    }
}
