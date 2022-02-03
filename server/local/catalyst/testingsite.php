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
 * Testing site settings.
 *
 * @package   local_catalyst
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');

require_login();

$context = context_system::instance();
require_capability('moodle/site:config', $context, $USER->id, true, "nopermissions");

$PAGE->set_url('/local/catalyst/testingsite.php');
$PAGE->set_context($context);

$mform = new local_catalyst_testingsite_form();

if ($mform->is_cancelled()) {
    redirect('');
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('testingsitesettings', 'local_catalyst'));

if ($data = $mform->get_data()) {
    // don't set anything if forced in config.php
    if (!array_key_exists('local_catalyst', $CFG->forced_plugin_settings) ||
        !array_key_exists('testingsite', $CFG->forced_plugin_settings['local_catalyst'])) {
        // Checkboxes in mforms don't return anything when empty so make sure we can turn it off.
        if (empty($data->testingsite)) {
            set_config('testingsite', 0, 'local_catalyst');
        } else {
            set_config('testingsite', $data->testingsite, 'local_catalyst');
        }
    }


    if (!array_key_exists('local_catalyst', $CFG->forced_plugin_settings) ||
        !array_key_exists('testingsiteallowlist', $CFG->forced_plugin_settings['local_catalyst'])) {

        set_config('testingsiteallowlist', trim($data->testingsiteallowlist), 'local_catalyst');
    }

    if (!array_key_exists('local_catalyst', $CFG->forced_plugin_settings) ||
        !array_key_exists('testingavailableto', $CFG->forced_plugin_settings['local_catalyst'])) {

        set_config('testingavailableto', trim($data->testingavailableto), 'local_catalyst');
    }
    echo $OUTPUT->notification(get_string('settingssaved', 'local_catalyst'), 'success');
}
$settings = (array)get_config('local_catalyst');
$mform->set_data($settings);

echo $OUTPUT->box_start('generalbox boxaligncenter', 'intro');
$mform->display();
echo $OUTPUT->box_end();
echo $OUTPUT->footer();
