<?php
/**
 * Defines the version of this module
 *
 * @package   local_clustercache
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->version  = 2014053100;
$plugin->requires = 2017111300; // Requires 3.4. (contains PHP 7 code.)
$plugin->component = 'local_clustercache'; // Full name of the plugin (used for diagnostics)
$plugin->cron     = 0;           // Period for cron to check this module (secs)
