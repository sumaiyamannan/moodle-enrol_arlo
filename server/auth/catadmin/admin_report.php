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
 * Page for deep admin reporting tool
 *
 * @package auth_catadmin
 * @copyright Peter Burnett (<peterburnett@catalyst-au.net)
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');

// Setup page.
admin_externalpage_setup('auth_catadmin_adminreport');

$prevurl = new moodle_url('/admin/settings.php', array('section' => 'authsettingcatadmin'));
$form = new \auth_catadmin\form\admin_report();

if ($form->is_cancelled()) {
    redirect($prevurl);
} else if ($form->no_submit_button_pressed()) {
    // Output auditing event for logging on button press.
    $auditevent = \auth_catadmin\event\users_audited::users_audited_event();
    $auditevent->trigger();

    // Save the last audit time in a plugin config.
    set_config('lastaudit', time(), 'auth_catadmin');

    // Reload page to display new timestamps.
    redirect($PAGE->url);
}

// Build the page output.
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('adminreport', 'auth_catadmin'));
$form->display();
echo $OUTPUT->footer();
