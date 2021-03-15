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

namespace auth_catadmin\task;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/user/lib.php');

/**
 * Suspends users after 1 day of inactivity.
 *
 * @package auth_catadmin\task
 */
class suspend_users extends \core\task\scheduled_task {

    public function get_name() {
        return get_string('tasksuspendusers', 'auth_catadmin');
    }

    public function execute($force = false) {
        global $DB, $CFG;

        if (intval(get_config('auth_catadmin', 'suspendafter')) > 0) {
            $suspendafter = time() - intval(get_config('auth_catadmin', 'suspendafter'));

            $users = $DB->get_records_sql(
                "SELECT * FROM {user} WHERE suspended = 0 AND auth = 'catadmin' AND lastaccess < :lastaccess",
                ['lastaccess' => $suspendafter]
            );
            foreach ($users as $user) {
                $user->suspended = 1;
                // Force logout.
                \core\session\manager::kill_user_sessions($user->id);
                user_update_user($user, false);
            }
        }

        if (intval(get_config('auth_catadmin', 'removeadminafter')) > 0) {
            $removeadminafter = time() - intval(get_config('auth_catadmin', 'removeadminafter'));

            $admins = explode(',', $CFG->siteadmins);
            $users = $DB->get_records_sql(
                "SELECT id FROM {user} WHERE auth = 'catadmin' AND lastaccess < :lastaccess",
                ['lastaccess' => $removeadminafter]);
            foreach ($users as $user) {
                $key = array_search($user->id, $admins);
                if ($key !== false && $key !== null) {
                    unset($admins[$key]);
                }
            }
            $logstringold = $CFG->siteadmins;
            $newadmins = implode(',', $admins);
            if ($newadmins != $logstringold) {
                set_config('siteadmins', implode(',', $admins));
                add_to_config_log('siteadmins', $logstringold, implode(',', $admins), 'core');
            }
        }

        return true;
    }
}
