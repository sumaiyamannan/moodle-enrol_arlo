<?php
/**
 * Version information
 *
 * @package    auth_catadmin
 * @copyright  Alex Morris <alex.morris@catadmin.net.nz>
 */

defined('MOODLE_INTERNAL') || die();

$plugin->version   = 2020092300;    // The current plugin version (Date: YYYYMMDDXX).
$plugin->release   = 2020092300;    // Match release exactly to version.
$plugin->requires  = 2016120509;    // Requires PHP 7, 2017051509 = T12. M3.3

$plugin->component = 'auth_catadmin';  // Full name of the plugin (used for diagnostics).
$plugin->maturity  = MATURITY_STABLE;
$plugin->dependencies = array(
    'auth_saml2' => 2020082101
);
