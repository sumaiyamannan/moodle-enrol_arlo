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
 * @package    auth_catadmin
 * @copyright  Alex Morris <alex.morris@catalyst.net.nz>
 */

$string['pluginname'] = 'Catadmin Authentication';
$string['auth_catadmindescription'] = 'Authentication plugin for Catalyst IT Staff';
$string['adminreport'] = 'Site Administrator Report';

$string['auditexemptadmin'] = 'Exempt primary admin';
$string['auditexemptadmin_desc'] = 'If checked the primary admin user will be exempt from password alerts.';

$string['debug'] = 'Debugging';
$string['debug_help'] = '<p>This adds extra debugging to the normal moodle log | <a href=\'{$a}\'>View SSP config</a></p>';

$string['select_idp_button'] = 'IdP Login';
$string['ipsubnets'] = 'IP based IdP Discovery';

$string['autoadmin'] = 'Auto admin';
$string['autoadmin_desc'] = 'If checked all Catalyst staff will automatically become site administrators.';
$string['removeadminafter'] = 'Remove admin after';
$string['removeadminafter_desc'] = 'Period of time after which admin is removed';
$string['suspendafter'] = 'Suspend user after';
$string['suspendafter_desc'] = 'Period of time after which the users are suspended';
$string['forceauthn'] = 'Force Authn';
$string['forceauthn_desc'] = 'This will force users to reauthenticate with the IdP if an existing IdP session exists.';
$string['groupattribute'] = 'Group attribute';
$string['groupattribute_desc'] = 'The attribute that contains the assigned groups from the IdP.';
$string['suffix'] = 'Suffix for catadmin users';
$string['suffix_desc'] = 'Adds a suffix to the end of user\'s username.';

$string["privacy:no_data_reason"] = "The catadmin authentication plugin does not store any personal data.";

$string['idpnamedefault'] = 'Catadmin Login';

$string['noattribute'] = 'You have logged in successfully but we could not find your \'{$a}\' attribute to associate you to an account in Moodle.';
$string['noidpfound'] = 'The IdP \'{$a}\' was not found as a configured IdP.';
$string['nouser'] = 'You have logged in successfully as \'{$a}\' but do not have an account in Moodle.';

$string['idpmetadata_badurl'] = 'Invalid metadata at {$a}';
$string['taskmetadatarefresh'] = 'Metadata refresh task';
$string['tasksuspendusers'] = 'Suspend users task';

$string['emailtaken'] = 'The email {$a} is already in use.';
$string['incorrectauthtype'] = 'You attempted to log in but your user account has a different authentication type.';
$string['nouser'] = 'The user {$a} does not exist in moodle.';
$string['noaccess'] = 'You do not have access to this site. You are missing group {$a}.';

$string['username'] = 'Username';
$string['userprofile'] = 'User profile';
$string['lastlogin'] = 'Last login';
$string['ipaddress'] = 'IP Address';
$string['loghistory'] = 'Log history';
$string['userlogs'] = 'User logs';

$string['tableheaderauthby'] = 'Authorised By';
$string['tableheadertimeauth'] = 'Authorised';
$string['tableauthlink'] = 'Audit log';
$string['tableheadertimereviewed'] = 'Last Audited';
$string['tableheaderlastaudit'] = 'Last Audit';
$string['tableheadertimechanged'] = 'Last Changed';
$string['tableheaderchangelevel'] = 'Admin';
$string['tablecontentisactive'] = '{$a}';
$string['tableaudit'] = 'Audit Completed';
$string['authname'] = 'ID: {$a->id} - {$a->name}';
$string['tablecontentnoauthoriser'] = 'Unable to locate authoriser';
$string['tablecontentnoauthtime'] = 'Unable to locate authorisation time';

$string['eventusersaudited'] = 'Catalyst User Audit';
$string['eventusersauditeddesc'] = 'Catalyst WRMS auth users audited';
$string['sendreminderemailtask'] = 'Send administrator audit reminder emails';
$string['noauditemailrequired'] = 'No audit email required.';
$string['auditemail'] = 'Audit email';
$string['auditemaildesc'] = 'If administrator access requires auditing, set this control to specify who recieves audit reminder emails.';
$string['auditheading'] = 'Audit Settings';
$string['auditheadingdesc'] = 'These settings control administrator auditing. Leave these as default unless administrator auditing is required.';
$string['auditemailcontent'] = 'An administrator access audit is required for {$a->site}. An administrator audit was last performed on {$a->date}.';
$string['auditemailcontentneveraudited'] = 'An administrator access audit is required for {$a}. An administrator audit has never been performed.';
$string['auditemaillink'] = 'Please click here to go the administrator report.';
$string['auditemailsubject'] = 'Administrator access audit required';
$string['auditemailsent'] = 'Administrator audit reminder email sent to {$a}.';
$string['auditduration'] = 'Audit period';
$string['auditduration_desc'] = 'If administrator access requires auditing, set this control to send a reminder email to the primary site admin when another administrator audit is required, after the duration from the last audit.';
$string['auditdisabled'] = 'Auditing is disabled. No users emailed.';
$string['audituseremailcontent'] = 'You must login to the system {$a->site} to continue retaining access to the system.

Your last login was on {$a->date}.

If no login is recorded, you may lose access to the system on {$a->lock}.';
$string['audituseremaillink'] = 'Click here to go to the login page.';
$string['audituseremailsubject'] = 'Login required for {$a}';
$string['audituseremailsent'] = 'Login reminder email sent to {$a}';

$string['messageprovider:audit'] = 'Administrator audit reminder emails.';
