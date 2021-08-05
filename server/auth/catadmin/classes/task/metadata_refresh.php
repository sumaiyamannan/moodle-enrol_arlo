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

namespace auth_catadmin\task;

use auth_catadmin\admin\setting_idpmetadata;
use auth_saml2\idp_parser;
use moodle_exception;

defined('MOODLE_INTERNAL') || die();

/**
 * Refresh metadata from IdP's
 *
 * @package auth_catadmin
 */
class metadata_refresh extends \core\task\scheduled_task {

    /**
     * @var idp_parser
     */
    private $idpparser;

    public function get_name() {
        return get_string('taskmetadatarefresh', 'auth_catadmin');
    }

    public function execute($force = false) {
        global $DB;

        $configidps = explode("\n", get_config('auth_catadmin', 'idpmetadata'));
        foreach ($configidps as $idpmetadata) {
            if (empty($idpmetadata)) {
                mtrace('IdP metadata not configured.');
                return false;
            }

            if (!$this->idpparser instanceof idp_parser) {
                $this->idpparser = new idp_parser();
            }

            if ($this->idpparser->check_xml($idpmetadata) == true) {
                mtrace('IdP metadata config not a URL, nothing to refresh.');
                return false;
            }

            $metadatasetting = new setting_idpmetadata();
            $metadatasetting->validate($idpmetadata);

            mtrace('IdP metadata refresh completed successfully.');
        }

        $existingidps = $DB->get_records('auth_catadmin_idps');
        foreach ($existingidps as $idp) {
            if (!in_array($idp->metadataurl, $configidps)) {
                $DB->delete_records('auth_catadmin_idps', ['metadataurl' => $idp->metadataurl]);
            }
        }

        return true;
    }
}
