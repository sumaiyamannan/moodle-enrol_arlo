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
 * Moves an item in an array based on its index. This is a helper function to
 * make reordering the list of auths easier.
 *
 * @param array $array - will modify the existing array provided.
 * @param int $from
 * @param int $to
 * @return never
 */
function array_move(array &$array, int $from, int $to) {
    $out = array_splice($array, $from, 1);
    array_splice($array, $to, 0, $out);
}

/**
 * Returns the auths in the order they would be installed as with catadmin.
 *
 * Takes in a csv of auths (typically from config) and reorders them based on
 * predefined rules, such as a $beforeauths list which ensures catadmin (this
 * plugin) will be prioritised ahead of the other auths in this listing.
 *
 * @param string $configauth - this would be in the format of values returned
 *               from get_config('core', 'auth').
 * @return string - the same values provided originally, but with catadmin added
 *                and reordered if required.
 */
function get_catadmin_auth_install_order(string $configauth = ''): string {

    // This will be the list of auths catadmin CAN go before, anything not listed
    // here will always be before catadmin, if already.
    $beforeauths = [
        // As per note: https://gitlab.wgtn.cat-it.co.nz/elearning/moodle-auth_catadmin/-/issues/29#note_643533 .
        'catalyst',
        'saml2',
        'oauth2',

        // Need confirmation before enabling.
        // 'basic',
        // 'mnet',
        // 'outage',
        // 'ldap_staff',
        // 'ldap',
        // 'lti',
        // 'webservice',
        // ...and any more.
    ];

    $existingauths = explode(',', $configauth);

    // Gets the current index of catadmin, plops it at the end if it's not already included.
    $catadminindex = array_search('catadmin', $existingauths, true);
    if (!$catadminindex) {
        $existingauths[] = 'catadmin';
        $catadminindex = count($existingauths) - 1;
    }

    // Moves catadmin before any of the items mentioned in the $befores listing.
    foreach ($beforeauths as $beforeauth) {
        if ($catadminindex === 0) {
            break;
        }

        // Ensure catadmin is before this auth.
        $beforeauthindex = array_search($beforeauth, $existingauths, true);
        // If it is already before this auth, do NOT move it. This also prevents
        // instances where it is moved after an unspecified client IdP, purely
        // because of reordering.
        if ($beforeauthindex !== false && $beforeauthindex < $catadminindex) {
            array_move($existingauths, $catadminindex, $beforeauthindex);
            $catadminindex = $beforeauthindex;
        }
    }

    return implode(',', $existingauths);
}
