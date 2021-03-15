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

namespace auth_catadmin;

defined('MOODLE_INTERNAL') || die();

use moodleform;

require_once("$CFG->libdir/formslib.php");

/**
 * Login form showing selection of the IdP's.
 *
 * @package auth_catadmin
 * @author Alex Morris <alex.morris@catalyst.net.nz>
 */
class idpselectform extends moodleform {

    public function definition() {
        $mform = $this->_form;

        $idpentityids = explode(PHP_EOL, get_config('auth_catadmin', 'idpmetadata'));

        $selectvalues = [];

        $rdidp = "";
        foreach ($idpentityids as $idpentity) {
            if (is_string($idpentity)) {
                $selectvalues[$idpentity] = $idpentity;
                if (date('z') % 2 == 0) {
                    $rdidp = $idpentity;
                }
            } else {
                foreach ((array) $idpentity as $subidpentity => $active) {
                    if ($active) {
                        if (date('z') % 2 == 0) {
                            $rdidp = $idpentity;
                        }
                        $selectvalues[$subidpentity] = $subidpentity;
                    }
                }
            }
        }

        $select = $mform->addElement('select', 'idp', get_string('select_idp_button', 'auth_catadmin'), $selectvalues);
        $select->setSelected($rdidp);

        $mform->addElement('submit', 'login', get_string('select_idp_button', 'auth_catadmin'));
    }
}
