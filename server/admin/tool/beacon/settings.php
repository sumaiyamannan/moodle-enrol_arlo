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
 * Plugin administration pages are defined here.
 *
 * @package     tool_beacon
 * @category    admin
 * @copyright   2020 Nicholas Hoobin <nicholashoobin@catalyst-au.net>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    // Beacon plugin settings.
    $settings = new admin_settingpage(
        'tool_beacon_settings', get_string('generalsettings', 'admin'));

    $settings->add(new admin_setting_configtext('tool_beacon/beaconbaseurl',
        new lang_string('beaconbaseurl', 'tool_beacon'),
        new lang_string('beaconbaseurldesc', 'tool_beacon') .
        ' ' . html_writer::link(new moodle_url('/admin/tool/beacon/reset.php'), get_string('reset', 'tool_beacon')),
        '', PARAM_URL));


    // Prepare beacon settings / report section.
    $section = 'toolbeacon';
    $ADMIN->add('tools', new admin_category('toolbeacon', get_string('pluginname', 'tool_beacon')));
    $ADMIN->add('toolbeacon', $settings);
    $ADMIN->add(
        $section,
        new admin_externalpage(
            'reporttoolbeacon',
            get_string('reporttitle', 'tool_beacon'),
            "$CFG->wwwroot/admin/tool/beacon/report.php"
        )
    );
}
