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
 * Reset beacon statistics
 *
 * @package    tool_beacon
 * @author     Brendan Heywood <brendan@catalyst-au.net>
 * @copyright  2021 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

require_admin();

$context = context_system::instance();
$name = get_string('reset', 'tool_beacon');

$PAGE->set_url(new moodle_url('/admin/tool/beacon/reset'));
$PAGE->set_context($context);
$PAGE->set_heading($name);
$PAGE->set_title($name);


$return = optional_param('return', null, PARAM_LOCALURL);

if (!optional_param('confirm', 0, PARAM_INT)) {
    echo $OUTPUT->confirm(get_string('beaconreset', 'tool_beacon'),
        new single_button(new moodle_url('/admin/tool/beacon/reset.php', [
                'confirm' => 1,
                'sesskey' => sesskey(),
                'return' => $return,
            ]),
            get_string('confirm')),
        new single_button(new moodle_url('/admin/settings.php', ['section' => 'tool_beacon_settings']),
            get_string('cancel'), false)
    );
    echo $OUTPUT->footer();
    exit;
}

// Delete all beacon stats, which are stored in this table.
require_sesskey();

$DB->delete_records('tool_beacon');

if ($return) {
    redirect($return);
}

echo $OUTPUT->notification(get_string('success'), 'notifysuccess');

echo $OUTPUT->header();
echo $OUTPUT->heading($name);

echo html_writer::link(new moodle_url('/admin/settings.php', ['section' => 'tool_beacon_settings']), get_string('back'));
echo html_writer::link(new moodle_url('/admin/settings.php', ['section' => 'tool_beacon_settings']), get_string('back'));

echo $OUTPUT->footer();
