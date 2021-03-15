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
 * Sends audit reminder emails when required.
 *
 * @package    auth_catadmin
 * @copyright  Catalyst IT
 * @author     Peter Burnett <peterburnett@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace auth_catadmin\task;

defined('MOODLE_INTERNAL') || die();

/**
 * Sends a reminder email to Audit administrators if too long since last audit
 */
class audit_email extends \core\task\scheduled_task {

    /**
     * Get a descriptive name for this task.
     *
     * @return string
     */
    public function get_name() {
        return get_string('sendreminderemailtask', 'auth_catadmin');
    }

    /**
     * Sends audit emails auditor, and to remind users to login.
     */
    public function execute() {
        $this->send_admin_email();
        $this->send_user_emails();
    }

    /**
     * This function sends an email to the auditing administrator when required
     *
     * @return void
     */
    private function send_admin_email() {
        global $SITE;

        $duration = get_config('auth_catadmin', 'auditperiod');
        $auditemail = get_config('auth_catadmin', 'auditemail');
        $lastaudited = get_config('auth_catadmin', 'lastaudit');

        // If not over period, or settings are empty, exit early.
        if ($duration == 0 || empty($auditemail) || $lastaudited > (time() - $duration)) {
            mtrace(get_string('noauditemailrequired', 'auth_catadmin'));
            return;
        }

        $auditurl = new \moodle_url('/auth/catadmin/admin_report.php');

        // Decide on message body based on when last audit was performed.
        if ($lastaudited != 0) {
            $date = userdate($lastaudited, get_string('strftimedatefullshort', 'langconfig'));
            $contentdata = ['site' => $SITE->fullname, 'date' => $date];
            $content = \html_writer::tag('p', get_string('auditemailcontent', 'auth_catadmin', $contentdata));
        } else {
            $content = \html_writer::tag('p', get_string('auditemailcontentneveraudited', 'auth_catadmin', $SITE->fullname));
        }
        $content .= \html_writer::tag('p', \html_writer::link($auditurl, get_string('auditemaillink', 'auth_catadmin')));

        $subject = get_string('auditemailsubject', 'auth_catadmin');
        $recipient = \core_user::get_user_by_email($auditemail);
        $sender = \core_user::get_noreply_user();

        // If not real user then construct an anonymous user to allow emails to go to an alias.
        if (empty($recipient)) {
            $recipient = new \stdClass();
            $recipient->id = -1;
            $recipient->email = $auditemail;
            $recipient->mailformat = 1;
        }
        email_to_user($recipient, $sender, $subject, $content, $content);

        mtrace(get_string('auditemailsent', 'auth_catadmin', $recipient->email));
    }

    private function send_user_emails() {
        global $DB, $SITE;

        $duration = get_config('auth_catalyst', 'auditperiod');
        $lastaudited = get_config('auth_catalyst', 'lastaudit');
        $auditemail = get_config('auth_catalyst', 'auditemail');
        $exemptadmin = get_config('auth_catalyst', 'auditexemptadmin');

        if ($duration == 0 || empty($auditemail)) {
            // The audit period is disabled, no users should be emailed.
            mtrace(get_string('auditdisabled', 'auth_catadmin'));
            return;
        }

        $sql = "SELECT *
                  FROM {user}
                 WHERE (auth = :auth OR " . $DB->sql_like('email', ':email') . ")
                   AND suspended = 0
                   AND deleted = 0
                   AND ((:time - currentlogin) / CAST(:duration AS decimal)) >= 0.8
              ORDER BY currentlogin DESC";
        $params = [
            'auth' => 'catadmin',
            'email' => '%@catalyst%',
            'time' => time(),
            'duration' => (int) $duration,
        ];

        $admin = get_admin();

        // Try to get a real user if one exists.
        $from = \core_user::get_user_by_email($auditemail);
        if (!$from) {
            $from = \core_user::get_noreply_user();
        }

        // We should only email active auth_cat users, or who have a catalyst email address.
        $catusers = $DB->get_records_sql($sql, $params);
        foreach ($catusers as $user) {

            if ($exemptadmin && $user->id == $admin->id) {
                continue;
            }

            $lastlogin = $user->currentlogin;

            $contentdata = [
                'site' => $SITE->fullname,
                'date' => userdate($lastlogin),
                'lock' => userdate(($lastaudited + $duration)),
            ];
            $content = \html_writer::tag('p', get_string('audituseremailcontent', 'auth_catadmin', $contentdata));

            $loginurl = new \moodle_url('/login/index.php');
            $content .= \html_writer::tag('p', \html_writer::link($loginurl, get_string('audituseremaillink', 'auth_catadmin')));

            $subject = get_string('audituseremailsubject', 'auth_catadmin', $SITE->fullname);

            email_to_user($user, $from, $subject, $content, $content);
            mtrace(get_string('audituseremailsent', 'auth_catadmin', $user->email));
        }
    }
}
