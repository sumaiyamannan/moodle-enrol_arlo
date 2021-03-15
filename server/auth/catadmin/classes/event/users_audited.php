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
 * Event for logging Catalyst user audits
 *
 * @package    auth_catadmin
 * @copyright  2019 Peter Burnett <peterburnett@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace auth_catadmin\event;

defined('MOODLE_INTERNAL') || die();

/**
 * Event for logging when someone has audited Catalyst WRMS users
 *
 * @package    auth_catadmin
 * @since      Moodle 3.3
 * @copyright  2019 Peter Burnett <peterburnett@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class users_audited extends \core\event\base {
    /**
     * Create instance of event.
     *
     * @return users_audited the audit event
     */
    public static function users_audited_event() {

        $data = array(
            'context' => \context_system::instance()
        );

        return self::create($data);
    }

    /**
     * Init method.
     *
     * @return void
     */
    protected function init() {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return get_string('eventusersauditeddesc', 'auth_catadmin');
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventusersaudited', 'auth_catadmin');
    }

    /**
     * Returns relevant URL.
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/auth/catadmin/admin_report.php');
    }
}
