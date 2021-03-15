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
 * Form for checking authorisation details
 *
 * @package auth_catadmin
 * @copyright Peter Burnett (<peterburnett@catalyst-au.net)
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');

defined('MOODLE_INTERNAL') || die();
global $DB;
$id = optional_param('id', 0, PARAM_INT);
$url = $url = new moodle_url($CFG->wwwroot . '/auth/catadmin/authby.php');

require_login();
require_capability('moodle/site:config', context_system::instance());

// This report can take ages to run.
\core\session\manager::write_close();

$PAGE->set_url($url);
$PAGE->set_context(context_system::instance());

$PAGE->set_title('User Authorisation');

$prevurl = new moodle_url($CFG->wwwroot . '/auth/catadmin/admin_report.php');
$authdata = get_authorsation_info($id);

$user = $DB->get_record('user', array('id' => $id));
$authdata['userid'] = (get_string('authname', 'auth_catadmin', array('id' => $user->id, 'name' => fullname($user, true))));

$form = new \auth_catadmin\form\authby();
$form->set_data($authdata);

if ($form->is_cancelled()) {
    redirect($prevurl);
} else {

    // Build the page output.
    echo $OUTPUT->header();
    $form->display();
    echo $OUTPUT->footer();
}
// Doing the authorisation check here, so URL PARAM doesnt need to be passed in.
function get_authorsation_info($userid) {
    global $DB;

    // Get log reader.
    $manager = get_log_manager();
    $readers = $manager->get_readers();
    $reader = reset($readers);

    // Grab recordset for user created records.
    $event = '\core\event\user_created';
    $select = "eventname = ? AND relateduserid = ?";

    $records = $reader->get_events_select($select, array($event, $userid), '', null, null);
    // Should only be one record.
    $record = reset($records);

    if (count($records) == 0) {
        return array('id' => get_string('tablecontentnoauthoriser', 'auth_catadmin'),
            'time' => get_string('tablecontentnoauthtime', 'auth_catadmin'));
    } else {
        $authoriser = $DB->get_record('user', array('id' => $record->userid));

        return array('id' => get_string('authname', 'auth_catadmin',
            array('id' => $authoriser->id, 'name' => fullname($authoriser, true))),
            'time' => userdate($record->timecreated));
    }
}
