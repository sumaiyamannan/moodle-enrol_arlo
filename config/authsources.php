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

defined('MOODLE_INTERNAL') || die();

global $catadminsaml, $CFG, $SITE, $SESSION;

// Check for https login.
$wwwroot = $CFG->wwwroot;
if (!empty($CFG->loginhttps)) {
    $wwwroot = str_replace('http:', 'https:', $CFG->wwwroot);
}

$config = [];

// Case for specifying no $SESSION IdP, select the first configured IdP as the default.
$arr = array_reverse($catadminsaml->metadataentities);
$metadataentities = array_pop($arr);
$idpentity = array_pop($metadataentities);
$idp = $idpentity->entityid;

if (!empty($SESSION->catadminidp)) {
    foreach ($catadminsaml->metadataentities as $idpentities) {
        foreach ($idpentities as $md5entityid => $idpentity) {
            if ($SESSION->catadminidp === $md5entityid) {
                $idp = $idpentity->entityid;
                break 2;
            }
        }
    }
}

$config[$catadminsaml->spname] = [
    'saml:SP',
    'entityID' => "$wwwroot/auth/catadmin/sp/metadata.php",
    'discoURL' => null,
    'idp' => $idp,
    'NameIDPolicy' => "urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified",
    'OrganizationName' => array(
        'en' => $SITE->shortname,
    ),
    'OrganizationDisplayName' => array(
        'en' => $SITE->fullname,
    ),
    'OrganizationURL' => array(
        'en' => $CFG->wwwroot,
    ),
    'privatekey' => $catadminsaml->spname . '.pem',
    'privatekey_pass' => get_config('auth_catadmin', 'privatekeypass'),
    'certificate' => $catadminsaml->spname . '.crt',
    'sign.logout' => true,
    'redirect.sign' => true,
    'signature.algorithm' => 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256',
    'ForceAuthn' => get_config('auth_catadmin', 'forceauthn') == "1",
];
