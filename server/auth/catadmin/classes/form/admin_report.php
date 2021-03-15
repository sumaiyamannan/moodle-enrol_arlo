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
 * Form for deep admin reporting tool
 *
 * @package auth_catadmin
 * @copyright Peter Burnett (<peterburnett@catalyst-au.net)
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

namespace auth_catadmin\form;

defined('MOODLE_INTERNAL') || die();
require_once("$CFG->libdir/formslib.php");

class admin_report extends \moodleform {
    public function definition() {
        $mform = $this->_form;

        $lastaudit = get_config('auth_catadmin', 'lastaudit');

        $mform->addElement('static', 'lastaudit', get_string('tableheaderlastaudit', 'auth_catadmin'), userdate($lastaudit));

        // Get table and render as HTML form element.
        $table = $this->generate_table();
        $mform->addElement('html', $table);

        $mform->registerNoSubmitButton('auditbutton');
        $buttonarray[] =& $mform->createElement('submit', 'auditbutton', get_string('tableaudit', 'auth_catadmin'));
        $buttonarray[] =& $mform->createElement('cancel', 'cancel', get_string('back'));
        // Add button group.
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
    }

    private function time_cell($time) {
        $format = get_string('strftimedate', 'langconfig');
        if ($time != 0) {
            $html = userdate($time, $format);
            $delta = time() - $time;
            $html .= "<br>";
            $delta = format_time($delta);
            $delta = preg_replace('/^(\S+\s+\S+)(\s.*)$/', '$1', $delta);
            $html .= $delta;
        } else {
            $html = get_string('never');
        }
        return $html;
    }

    private function generate_table() {
        global $DB, $CFG;

        // Get all catalyst users or current siteadmins.
        list($sqlin, $sqlparams) = $DB->get_in_or_equal(explode(',', $CFG->siteadmins));
        $sql = "SELECT *
                  FROM {user}
                 WHERE auth = ?
                    OR " . $DB->sql_like('email', '?') . "
                    OR id {$sqlin}
              ORDER BY currentlogin DESC";
        // Add siteadmins params to end of anonymous params array.
        $finalparams = array_merge(array('catadmin', '%@catalyst%'), $sqlparams);
        $catusers = $DB->get_records_sql($sql, $finalparams);

        // Get all lang strings for table header.
        $stringsreqd = array(
            'tableheadertimeauth',
            'tableheaderauthby',
            'tableheadertimereviewed',
            'tableheadertimechanged',
            'tableheaderchangelevel',
        );
        $stringarr = get_strings($stringsreqd, 'auth_catadmin');

        $table = new \html_table();
        $table->head = array(
            get_string('fullname'),
            $stringarr->tableheadertimeauth,
            $stringarr->tableheaderauthby,
            $stringarr->tableheadertimechanged,
            $stringarr->tableheaderchangelevel,
            get_string('active'),
            get_string('lastaccess'),
        );
        $auditperiod = get_config('auth_catadmin', 'auditperiod');

        foreach ($catusers as $user) {
            $userid = $user->id;
            $fullname = fullname($user);
            $fullname = \html_writer::tag('span', $fullname);
            if ($user->suspended || $user->deleted) {
                $fullname = \html_writer::tag('del', $fullname);
            }
            $fullname = \html_writer::link('/user/profile.php?id=' . $user->id, $fullname);
            $fullname .= "<br>" . $user->email;

            // Check if timecreated is set.
            $authtime = $this->time_cell($user->timecreated);

            // Setup authorised page link.
            $url = new \moodle_url('/auth/catadmin/authby.php', array('id' => $userid));
            $authstring = get_string('tableauthlink', 'auth_catadmin');
            $authlink = \html_writer::link($url, $authstring);

            // Get Time of last access level change.
            $changerecord = $this->get_access_level_change_record($user);            // Check record was found.

            if ($changerecord !== false) {
                $timechanged = $changerecord->timemodified;
            } else {
                $timechanged = 0;
            }
            $timechanged = $this->time_cell($timechanged);

            // Get level after change.
            $changelevel = $this->get_access_level($user);

            // Get withdrawn status.
            $active = $this->is_active($user);

            // Get last access.
            $lastaccess = $this->last_access($user);

            $data = array(
                $fullname,
                $authtime,
                $authlink,
                $timechanged,
                $changelevel,
                $active,
                $lastaccess,
            );

            // Construct row of data.
            $row = new \html_table_row($data);
            $row->attributes['class'] = $this->get_row_class($user, $auditperiod);

            $table->data[] = $row;
        }

        return \html_writer::table($table);
    }

    // ========================================HELPER FUNCTIONS=================================================

    private function get_access_level_change_record($user) {
        global $DB;
        if (!$DB->sql_regex_supported()) {
            return '';
        }

        // Construct the regex and operators for query.
        $id = $user->id;
        $regex = "(,\s?$id,|^$id,|,\s?$id$)";
        $dbregexfound = $DB->sql_regex();
        $dbregexnotfound = $DB->sql_regex(false);

        // get entries where id was found in value, and not in old value OR
        // where id was not found in value, but found in old value.
        $sql = "SELECT *
                 FROM {config_log}
                WHERE name = 'siteadmins'
                  AND ((value $dbregexfound ? AND oldvalue $dbregexnotfound ?)
                        OR
                      (value $dbregexnotfound ? AND oldvalue $dbregexfound ?))";
        $records = $DB->get_records_sql($sql, array($regex, $regex, $regex, $regex));

        // Order records by timemodified.
        usort($records, function($a, $b) {
            return $a->timemodified < $b->timemodified;
        });

        return reset($records);
    }

    private function get_access_level($user) {
        global $CFG;
        $admins = explode(',', $CFG->siteadmins);

        if (in_array($user->id, $admins)) {
            $html = get_string('yes');
        } else {
            $html = get_string('no');
        }
        $admin = get_admin();
        if ($user->id == $admin->id) {
            $html = get_string('admin');
        }
        $html =
            \html_writer::link(new \moodle_url('/admin/roles/admins.php', array('addselect_searchtext' => fullname($user))), $html);
        return $html;
    }

    private function is_active($user) {
        if ($user->deleted) {
            return get_string('deleted');
        }
        if ($user->suspended) {
            return get_string('suspended');
        }
        return get_string('yes');
    }

    private function last_access($user) {

        if ($user->suspended || $user->deleted) {
            return $this->time_cell($user->timemodified);
        } else {
            return $this->time_cell($user->currentlogin);
        }
    }

    private function get_row_class($user, $auditperiod) {

        // If the auditperiod is 0, we shouldn't apply any styling.
        if (empty($auditperiod)) {
            return '';
        }

        $lastloginperiod = (time() - $user->currentlogin);
        $active = !($user->suspended || $user->deleted);

        if (!$active) {
            return 'table-secondary';
        } else if ($lastloginperiod > $auditperiod) {
            return 'table-danger';
        } else if ($lastloginperiod / $auditperiod > 0.8) {
            return 'table-warning';
        }
        return '';
    }
}
