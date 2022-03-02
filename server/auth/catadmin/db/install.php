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
 * Enable the plugin by default
 *
 * @package     auth_catadmin
 * @copyright   2020 Alex Morris <alex.morris@catalyst.net.nz>
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once("{$CFG->dirroot}/auth/catadmin/setuplib.php");
require_once(__DIR__ . '/upgradelib.php');

/**
 * Enable the plugin on installation
 */
function xmldb_auth_catadmin_install() {
    if (!defined('PHPUNIT_TEST') || !PHPUNIT_TEST) {
        set_config('privatekeypass', get_site_identifier(), 'auth_catadmin');
        $existingauths = get_config('core', 'auth');
        $reorderedauths = get_catadmin_auth_install_order($existingauths);
        set_config('auth', $reorderedauths);

        $catadminsaml = new auth_plugin_catadmin();
        $catadminsaml->initialise();

        $metadata = new \auth_catadmin\task\metadata_refresh();
        $metadata->execute();
    }
}
