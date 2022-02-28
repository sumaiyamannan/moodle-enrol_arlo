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

require_once(__DIR__ . '/../saml2/.extlib/simplesamlphp/vendor/autoload.php');

// Autoload SimpleSAMLphp libraries from auth_saml2.
spl_autoload_register(
    function($classname) {
        $map = [
            'SAML2' => 'saml2/src/',
        ];
        foreach ($map as $namespace => $subpath) {
            $classpath = explode('_', $classname);
            if ($classpath[0] != $namespace) {
                $classpath = explode('\\', $classname);
                if ($classpath[0] != $namespace) {
                    continue;
                }
            }
            $subpath = __DIR__ . '/' . $subpath;
            $filepath = $subpath . implode('/', $classpath) . '.php';
            if (file_exists($filepath)) {
                require_once($filepath);
            }
        }
    }
);
