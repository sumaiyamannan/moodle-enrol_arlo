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

require_once(__DIR__ . '/../../config.php');
require('setup.php');

// @codingStandardsIgnoreStart
global $CFG, $DB, $USER, $SESSION, $PAGE, $catadminsaml;
// @codingStandardsIgnoreEnd

require_once("$CFG->dirroot/login/lib.php");
require_once(__DIR__ . '/classes/idpselectform.php');

$PAGE->set_context(context_system::instance());
$PAGE->set_url('/auth/catadmin/login.php');
$PAGE->set_title('Catalyst SSO Login');
$PAGE->set_heading('Catalyst SSO Login');

$idps = auth_catadmin_get_idps(false, true);
$idpentityids = array();
foreach ($idps as $idpid => $idparray) {
    $idp = array_shift($idparray);
    $idpentityids[] = $idp['entityid'];
}
$data = [
    'idpentityids' => $idpentityids
];

if (count($idpentityids) == 1) {
    $SESSION->catadminidp = md5($idpentityids[0]);
}

$idp = trim(optional_param('idp', '', PARAM_RAW));

if (!empty($idp)) {
    $SESSION->catadminidp = md5($idp);
}

$auth = new SimpleSAML\Auth\Simple($catadminsaml->spname);
if (!empty($SESSION->catadminidp)) {
    $auth->requireAuth();
}

$action = new moodle_url('/auth/catadmin/login.php');
$mform = new \auth_catadmin\idpselectform($action, $data);
if ($form = $mform->get_data()) {
    if (!$auth->isAuthenticated()) {
        $auth->requireAuth();
    } else {
        $catadminsaml->saml_login();
    }
} else {
    if (!$auth->isAuthenticated()) {
        $mform->display();
    } else {
        $catadminsaml->saml_login();
    }
}
