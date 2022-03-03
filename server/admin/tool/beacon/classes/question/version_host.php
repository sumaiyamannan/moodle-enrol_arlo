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
 * VERSION_HOST question type.
 *
 * @package     tool_beacon
 * @copyright   2020 Nicholas Hoobin <nicholashoobin@catalyst-au.net>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_beacon\question;

use tool_beacon\model\beacon_row_kv;

class version_host extends question {

    protected function query() {
        global $CFG;

        // I know , I know! Used to obtain maturity which is not found elsewhere.
        require("$CFG->dirroot/version.php");

        $query = [
            'version' => get_config('core', 'version'),
            'release' => get_config('core', 'release'),
            'branch' => get_config('core', 'branch'),
            'maturity' => $maturity
        ];

        foreach ($query as $key => $value) {
            $siteinfo[] = new beacon_row_kv(
                $this->domain,
                $this->timestamp,
                $this->type,
                $this->questionid,
                'version_host',
                $key,
                $value
            );
        }

        return $siteinfo;
    }
}
