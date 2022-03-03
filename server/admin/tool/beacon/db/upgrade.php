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
 * Upgrade logic.
 *
 * @package   tool_beacon
 * @author    Kevin Pham <kevinpham@catalyst-au.net>
 * @copyright Catalyst IT, 2021
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Performs data migrations and updates on upgrade.
 *
 * @param   integer   $oldversion
 * @return  boolean
 */
function xmldb_tool_beacon_upgrade($oldversion = 0) {
    global $CFG, $DB;

    require_once($CFG->libdir.'/db/upgradelib.php'); // Core Upgrade-related functions.

    $dbman = $DB->get_manager(); // Loads ddl manager and xmldb classes.

    if ($oldversion < 2020061801) {
        // Define table beacon to be created.
        $table = new xmldb_table('beacon');

        // Adding fields to table beacon.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('questionid', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timeanswered', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table beacon.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Conditionally launch create table for beacon.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Beacon savepoint reached.
        upgrade_plugin_savepoint(true, 2020061801, 'tool', 'beacon');
    }

    if ($oldversion < 2022020300) {
        // Rename table for tool_beacon.
        $table = new xmldb_table('beacon');
        $dbman->rename_table($table, 'tool_beacon');

        // Define field answer to be added to tool_beacon.
        $table = new xmldb_table('tool_beacon');

        // Conditionally launch add field answer.
        $field = new xmldb_field('answer', XMLDB_TYPE_TEXT, null, null, null, null, null, 'timeanswered');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Conditionally launch add field type.
        $field = new xmldb_field('type', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'id');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Beacon savepoint reached.
        upgrade_plugin_savepoint(true, 2022020300, 'tool', 'beacon');
    }
    return true;
}
