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
 * REGISTRATION question type.
 *
 * @package     tool_beacon
 * @copyright   2020 Nicholas Hoobin <nicholashoobin@catalyst-au.net>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_beacon\question;

use tool_beacon\model\beacon_row_kv;

class registration extends question {

    protected function query() {
        global $CFG;

        $siteinfo = [];

        $totara = $CFG->dirroot . '/' . $CFG->admin . '/registerlib.php';
        $registrationmanagerlib = $CFG->dirroot . '/' . $CFG->admin . '/registration/lib.php';

        if (class_exists('\core\hub\registration')) {
            $func = \core\hub\registration::get_site_info();
        } else if (\stream_resolve_include_path($registrationmanagerlib) !== false) {
            require_once($registrationmanagerlib);
            $registrationmanager = new \registration_manager();
            $func = $registrationmanager->get_site_info(HUB_MOODLEORGHUBURL);
        } else if (\stream_resolve_include_path($totara) !== false) {
            require_once($totara);
            $func = get_registration_data();
        }

        foreach ($func as $key => $value) {
            $siteinfo[] = new beacon_row_kv(
                $this->domain,
                $this->timestamp,
                $this->type,
                $this->questionid,
                'site',
                $key,
                $value
            );
        }

        return $siteinfo;
    }
}
