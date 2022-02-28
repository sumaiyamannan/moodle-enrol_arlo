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

namespace auth_catadmin\form;

defined('MOODLE_INTERNAL') || die();
require_once("$CFG->libdir/formslib.php");

class authby extends \moodleform {

    public function definition() {
        $mform = $this->_form;
        $mform->addElement('header', 'authheading', get_string('tableheaderauthby', 'auth_catadmin'));

        // Display of information.
        $mform->addElement('static', 'userid', get_string('userprofile', 'auth_catadmin'), '');
        $mform->addElement('static', 'id', get_string('tableheaderauthby', 'auth_catadmin'), '');
        $mform->addElement('static', 'time', get_string('tableheadertimeauth', 'auth_catadmin'), '');

        // Add cancel button.
        $buttonarray[] =& $mform->createElement('cancel', 'cancel', get_string('back'));
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        return $errors;
    }
}

