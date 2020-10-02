<?php
/**
 * Auto load libraries
 *
 * @package    auth_catadmin
 * @copyright  Alex Morris <alex.morris@catadmin.net.nz>
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../saml2/.extlib/simplesamlphp/vendor/autoload.php');

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
