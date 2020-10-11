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
 * Contains class testingsite_form
 *
 * @package   local_catalyst
 * @copyright 2019 Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Class testingsite_form
 *
 * @package   local_catalyst
 * @copyright 2019 Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_catalyst_testingsite_form extends moodleform {
    /**
     * Form definition
     */
    public function definition () {
        $mform =& $this->_form;
        global $CFG;
        if (array_key_exists('local_catalyst', $CFG->forced_plugin_settings) and
            array_key_exists('testingsite', $CFG->forced_plugin_settings['local_catalyst'])) {
            $mform->addElement('static', '', '', '<span style="color:red">'.get_string('testingsiteforced', 'local_catalyst')."</span>");
        } else {
            $mform->addElement('checkbox', 'testingsite', get_string('testingsite', 'local_catalyst'));
        }

        if (array_key_exists('local_catalyst', $CFG->forced_plugin_settings) and
            array_key_exists('testingsiteallowlist', $CFG->forced_plugin_settings['local_catalyst'])) {
            $mform->addElement('static', '', get_string('testingsiteallowlist', 'local_catalyst'),
                '<span style="color:red">'.get_string('testingsiteforced', 'local_catalyst')."</span>");

        } else {
            $mform->addElement('text', 'testingsiteallowlist', get_string('testingsiteallowlist', 'local_catalyst'));
            $mform->addHelpButton('testingsiteallowlist', 'testingsiteallowlist', 'local_catalyst');
            $mform->setType('testingsiteallowlist', PARAM_TEXT);
        }

        if (array_key_exists('local_catalyst', $CFG->forced_plugin_settings) and
            array_key_exists('testingavailableto', $CFG->forced_plugin_settings['local_catalyst'])) {

            $mform->addElement('static', '', get_string('testingavailableto', 'local_catalyst'),
                '<span style="color:red">'.get_string('testingsiteforced', 'local_catalyst')."</span>");
        } else {
            $mform->addElement('date_selector', 'testingavailableto', get_string('testingavailableto', 'local_catalyst'));
            $mform->addHelpButton('testingavailableto', 'testingavailableto', 'local_catalyst');
            $mform->setDefault('testingavailableto', time());
        }

        $this->add_action_buttons(true);
    }
}