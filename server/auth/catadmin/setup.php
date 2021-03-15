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
 * Setup
 *
 * @package     auth_catadmin
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/setuplib.php');

global $CFG, $catadminsaml;

if (isset($CFG->sslproxy) && $CFG->sslproxy) {
    $_SERVER['SERVER_PORT'] = '443';
}

$catadminsaml = new auth_plugin_catadmin();
$catadminsaml->initialise();

$catadminsaml->get_catadmin_directory();
if (!file_exists($catadminsaml->certpem) || !file_exists($catadminsaml->certcrt)) {
    $error = create_catadmin_certificates($catadminsaml);
    if ($error) {
        // @codingStandardsIgnoreStart
        error_log($error);
        // @codingStandardsIgnoreEnd
    }
}

SimpleSAML\Configuration::setConfigDir("$CFG->dirroot/auth/catadmin/config");
