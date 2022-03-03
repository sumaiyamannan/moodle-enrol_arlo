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
 * Beacon transparency report
 *
 * @package   tool_beacon
 * @author    Kevin Pham <kevinpham@catalyst-au.net>
 * @copyright Catalyst IT, 2022
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_OUTPUT_BUFFERING', true);
use tool_beacon\report_table;

require_once(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');

$download = optional_param('download', '', PARAM_ALPHA);

admin_externalpage_setup('reporttoolbeacon', '', null, '', array('pagelayout' => 'report'));

$url = new moodle_url('/admin/tool/beacon/report.php');
$table = new report_table('transparency', $url);
$table->is_downloading($download, 'beacon-transparency-report');
$table->setup();
if (!$table->is_downloading()) {
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('reporttitle', 'tool_beacon'));

    $reseturl = new moodle_url('/admin/tool/beacon/reset.php', [
        'confirm' => 1,
        'sesskey' => sesskey(),
        'return' => $url,
    ]);
    echo $OUTPUT->action_link(
        $reseturl,
        get_string('reset', 'tool_beacon') . $OUTPUT->pix_icon('t/delete', get_string('reset', 'tool_beacon')),
        new confirm_action(get_string('beaconreset', 'tool_beacon'))
    );

    echo ' ';

    $cronurl = new moodle_url('/admin/tool/task/schedule_task.php?', [
        'task' => 'tool_beacon\task\signal_beacon',
        'confirm' => 1,
        'sesskey' => sesskey(),
        'return' => $url,
    ]);
    echo $OUTPUT->action_link(
        $cronurl,
        get_string('runtask', 'tool_beacon') . $OUTPUT->pix_icon('e/redo', get_string('runtask', 'tool_beacon')),
        new confirm_action(get_string('confirm'))
    );

}

$table->out(100, true);

if (!$table->is_downloading()) {
    echo $OUTPUT->footer();
}

