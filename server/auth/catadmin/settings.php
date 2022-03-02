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
 * Admin settings and defaults.
 *
 * @package auth_catadmin
 * @copyright Alex Morris <alex.morris@catalyst.net.nz>
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $authplugin = get_auth_plugin('catadmin');

    $options = [
        auth_plugin_catadmin::AUTOADMIN_YES => get_string('yes'),
        auth_plugin_catadmin::AUTOADMIN_NO_PRESERVE => get_string('autoadmin_nopreserve', 'auth_catadmin'),
        auth_plugin_catadmin::AUTOADMIN_NO_STRICT => get_string('autoadmin_nostrict', 'auth_catadmin'),
    ];

    $settings->add(new admin_setting_heading('auth_catadmin/pluginname', '',
        new lang_string('auth_catadmindescription', 'auth_catadmin')));

    $settings->add(new admin_setting_configiplist('auth_catadmin/ipsubnets', new lang_string('ipsubnets', 'auth_catadmin'),
        new lang_string('ipblockersyntax', 'admin'), ''));

    $settings->add(new admin_setting_configselect('auth_catadmin/autoadmin',
        get_string('autoadmin', 'auth_catadmin'),
        get_string('autoadmin_desc', 'auth_catadmin'), auth_plugin_catadmin::AUTOADMIN_YES, $options));

    $settings->add(new admin_setting_configduration('auth_catadmin/suspendafter',
        get_string('suspendafter', 'auth_catadmin'),
        get_string('suspendafter_desc', 'auth_catadmin'), 0, DAYSECS));

    $settings->add(new admin_setting_configduration('auth_catadmin/removeadminafter',
        get_string('removeadminafter', 'auth_catadmin'),
        get_string('removeadminafter_desc', 'auth_catadmin'), 0, DAYSECS));

    $settings->add(new admin_setting_configcheckbox('auth_catadmin/forceauthn',
        get_string('forceauthn', 'auth_catadmin'),
        get_string('forceauthn_desc', 'auth_catadmin'), 0));

    $settings->add(new admin_setting_configtext('auth_catadmin/groupattribute',
        get_string('groupattribute', 'auth_catadmin'),
        get_string('groupattribute_desc', 'auth_catadmin'), 'affiliations', PARAM_TEXT));

    $settings->add(new admin_setting_configtext('auth_catadmin/groups',
        get_string('groups', 'auth_catadmin'),
        get_string('groups_desc', 'auth_catadmin'), 'elearning', PARAM_TEXT));

    $settings->add(new admin_setting_configtext('auth_catadmin/suffix',
        get_string('suffix', 'auth_catadmin'),
        get_string('suffix_desc', 'auth_catadmin'), ''));

    $settings->add(new admin_setting_heading(
        'auth_catadmin/auditsettings',
        get_string('auditheading', 'auth_catadmin'),
        get_string('auditheadingdesc', 'auth_catadmin')
    ));

    $settings->add(new admin_setting_configtext(
        'auth_catadmin/auditemail',
        get_string('auditemail', 'auth_catadmin'),
        get_string('auditemaildesc', 'auth_catadmin'),
        '', PARAM_EMAIL
    ));

    $settings->add(new admin_setting_configduration(
        'auth_catadmin/auditperiod',
        get_string('auditduration', 'auth_catadmin'),
        get_string('auditduration_desc', 'auth_catadmin'),
        0, DAYSECS
    ));

    $settings->add(new admin_setting_configcheckbox('auth_catadmin/auditexemptadmin',
        new lang_string('auditexemptadmin', 'auth_catadmin'),
        new lang_string('auditexemptadmin_desc', 'auth_catadmin'), 0));

    $adminreport = html_writer::link(new moodle_url('/auth/catadmin/admin_report.php'), get_string('adminreport', 'auth_catadmin'));
    $settings->add(new admin_setting_heading('auth_catadmin/admin', '', $adminreport));


    // Display locking / mapping of profile fields.
    $help = get_string('auth_updatelocal_expl', 'auth');
    $help .= get_string('auth_fieldlock_expl', 'auth');
    $help .= get_string('auth_updateremote_expl', 'auth');
    display_auth_lock_options($settings, $authplugin->authtype, $authplugin->userfields, $help, true, true,
            $authplugin->get_custom_user_profile_fields());
}

// Add External admin page for admin access auditing.
$ADMIN->add('reports', new admin_externalpage('auth_catadmin_adminreport',
    get_string('adminreport', 'auth_catadmin'),
    new moodle_url('/auth/catadmin/admin_report.php')));
