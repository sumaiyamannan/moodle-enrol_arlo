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
 * Group observers.
 *
 * @package    local_catalyst
 * @copyright  2019 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_catalyst;
defined('MOODLE_INTERNAL') || die();

/**
 * Group observers class.
 *
 * @package    local_catalyst
 * @copyright  2019 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class group_observers {

    /**
     * A user login event.
     *
     * @param \core\event\base $event The event.
     * @return void
     */
    public static function user_login($event) {
        global $USER, $CFG;

        if (empty($CFG->siteadmins) || !empty($CFG->adminsetuppending)) {
            return;
        }

        $catconfig = get_config('local_catalyst');
        if (empty($catconfig->testingavailableto)) {
            // If not set in db, set to 0.
            $catconfig->testingavailableto = 0;
        }
        if (!empty($catconfig->testingsite) && $catconfig->testingavailableto < time()) {
            // This is a catalyst test site with an expired time, check for user access.
            if (!in_array(substr($USER->email, strpos($USER->email, '@')),
                explode(",", get_config('local_catalyst', 'testingsitewhitelist')))) {
                // This is not a catalyst user, force logout and redirect to catalyst error page.
                require_logout();
                redirect($CFG->wwwroot.'/local/catalyst/login.php');
            }
        }
    }
}
