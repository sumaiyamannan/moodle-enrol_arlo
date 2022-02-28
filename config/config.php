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

global $CFG, $catadminsaml;

$metadatasources = [];
foreach ($catadminsaml->metadataentities as $metadataurl => $idpentities) {
    $metadatasources[] = [
        'type' => 'xml',
        'file' => "$CFG->dataroot/catadmin/" . md5($metadataurl) . ".idp.xml"
    ];
}

$config = array(
    'baseurlpath' => $CFG->wwwroot . '/auth/catadmin/sp/',
    'certdir'           => $catadminsaml->get_catadmin_directory() . '/',
    'debug'             => $catadminsaml->config->debug ? true : false,
    'logging.level'     => $catadminsaml->config->debug ? SimpleSAML\Logger::DEBUG : SimpleSAML\Logger::ERR,
    'logging.handler'   => $catadminsaml->config->logtofile ? 'file' : 'errorlog',
    'loggingdir'        => $catadminsaml->config->logdir,
    'logging.logfile'   => 'simplesamlphp.log',
    'showerrors'        => $CFG->debugdisplay ? true : false,
    'errorreporting'    => false,
    'debug.validatexml' => false,
    'secretsalt'        => get_config('auth_catadmin', 'privatekeypass'),
    'technicalcontact_name'  => $CFG->supportname ? $CFG->supportname : 'Admin User',
    'technicalcontact_email' => $CFG->supportemail ? $CFG->supportemail : $CFG->noreplyaddress,
    'timezone' => class_exists('core_date') ? core_date::get_server_timezone() : null,

    'session.duration'          => 60 * 60 * 8, // 8 hours.
    'session.datastore.timeout' => 60 * 60 * 4,
    'session.state.timeout'     => 60 * 60,

    'session.authtoken.cookiename'  => 'MDL_SSP_AuthToken',
    'session.cookie.name'     => 'MDL_SSP_SessID',
    'session.cookie.path'     => $CFG->sessioncookiepath,
    'session.cookie.domain'   => null,
    'session.cookie.secure'   => !empty($CFG->cookiesecure),
    'session.cookie.lifetime' => 0,

    'session.phpsession.cookiename' => null,
    'session.phpsession.savepath'   => null,
    'session.phpsession.httponly'   => true,

    'enable.http_post' => false,

    'signature.algorithm' => 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256',

    'metadata.sign.enable'          => $catadminsaml->config->spmetadatasign ? true : false,
    'metadata.sign.certificate'     => $catadminsaml->certcrt,
    'metadata.sign.privatekey'      => $catadminsaml->certpem,
    'metadata.sign.privatekey_pass' => get_config('auth_catadmin', 'privatekeypass'),
    'metadata.sources'              => $metadatasources,

    'store.type' => !empty($CFG->auth_catadmin_store) ? $CFG->auth_catadmin_store : '\\auth_catadmin\\store',

    'proxy' => null,

    'authproc.sp' => array(
        50 => array(
            'class' => 'core:AttributeMap',
            'oid2name',
        ),
    )
);
