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
 * Base class for unit tests for auth_manual.
 *
 * @package    auth_catadmin
 * @category   test
 * @author     Kevin Pham <kevinpham@catalyst-au.net>
 * @copyright  Catalyst IT, 2021
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(__DIR__ . '/../db/upgradelib.php');


class auth_catadmin_test extends advanced_testcase {

    public function test_install_auth_order_catadmin_before_saml2() {
        $authconfig = 'unknown,unknown2,saml2,unknown3,catadmin';
        $expected = 'unknown,unknown2,catadmin,saml2,unknown3';
        $reordered = get_catadmin_auth_install_order($authconfig);
        $this->assertEquals($expected, $reordered);
    }

    public function test_install_auth_order_catadmin_does_not_go_ahead_of_unknowns_not_in_the_beforelist() {
        $authconfig = 'unknown,unknown2,saml2,catadmin';
        $expected = 'unknown,unknown2,catadmin,saml2';
        $reordered = get_catadmin_auth_install_order($authconfig);
        $this->assertEquals($expected, $reordered);
    }

    public function test_install_auth_order_catadmin_already_before_options_in_beforelist_does_not_reorder() {
        $authconfig = 'unknown,catadmin,unknown2,saml2';
        $expected = 'unknown,catadmin,unknown2,saml2';
        $reordered = get_catadmin_auth_install_order($authconfig);
        $this->assertEquals($expected, $reordered);
    }

    public function test_install_auth_order_catadmin_gracefully_inserts_on_install() {
        $authconfig = 'unknown,unknown2,unknown3';
        $expected = 'unknown,unknown2,unknown3,catadmin';
        $reordered = get_catadmin_auth_install_order($authconfig);
        $this->assertEquals($expected, $reordered);

        $authconfig = 'unknown,saml2,unknown2,unknown3';
        $expected = 'unknown,catadmin,saml2,unknown2,unknown3';
        $reordered = get_catadmin_auth_install_order($authconfig);
        $this->assertEquals($expected, $reordered);
    }

}
