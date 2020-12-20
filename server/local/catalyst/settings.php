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
 * Local Catalyst module for any Catalyst-specific stuff
 *
 * @package local_catalyst
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;
global $USER;

// Only show these to catadmin/catalyst accounts that have config ability.
if ($hassiteconfig &&
    in_array(substr($USER->email, strpos($USER->email, '@')),
             explode(",", get_config('local_catalyst', 'testingsitewhitelist')))) {

    $ADMIN->add('localplugins', new admin_externalpage('local_catalyst', get_string('pluginname', 'local_catalyst'), $CFG->wwwroot.'/local/catalyst/testingsite.php'));

}
