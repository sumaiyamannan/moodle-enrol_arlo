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

defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/upgradelib.php');

function xmldb_auth_catadmin_upgrade($oldversion) {
    global $CFG, $DB;

    if ($oldversion < 2020042801) {
        if ($DB->record_exists('user', ['username' => 'catadmin', 'deleted' => 0, 'mnethostid' => $CFG->mnet_localhost_id])) {
            $catadminid = $DB->get_field_select('user', 'id', 'username = ? and deleted = 0 and mnethostid = ?',
                array('catadmin', $CFG->mnet_localhost_id));
            $DB->update_record('user', ['id' => $catadminid, 'firstname' => 'Catalyst']);
            $DB->update_record('user', ['id' => $catadminid, 'lastname' => 'TestAccount']);

            $admins = explode(',', $CFG->siteadmins);
            $key = array_search($catadminid, $admins);
            if ($key !== false && $key !== null) {
                unset($admins[$key]);
                set_config('siteadmins', implode(',', $admins));
            }
        }
        upgrade_plugin_savepoint(true, 2020042801, 'auth', 'catadmin');
    }

    if ($oldversion < 2020092300) {
        upgrade_plugin_savepoint(true, 2020092300, 'auth', 'catadmin');
    }

    if ($oldversion < 2021021800) {
        upgrade_plugin_savepoint(true, 2021021800, 'auth', 'catadmin');
    }

    if ($oldversion < 2021022400) {
        upgrade_plugin_savepoint(true, 2021022400, 'auth', 'catadmin');
    }

    if ($oldversion < 2021121000) {
        $existingauths = get_config('core', 'auth');
        $reorderedauths = get_catadmin_auth_install_order($existingauths);
        set_config('auth', $reorderedauths);
        upgrade_plugin_savepoint(true, 2021121000, 'auth', 'catadmin');
    }

    return true;
}
