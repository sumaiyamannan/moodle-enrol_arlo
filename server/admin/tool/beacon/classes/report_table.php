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

namespace tool_beacon;

/**
 * Display table for beacon report.
 *
 * @package   tool_beacon
 * @author    Kevin Pham <kevinpham@catalyst-au.net>
 * @copyright Catalyst IT, 2022
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_table extends \table_sql {

    const COLUMNS = [
        'questionid',
        'type',
        'timeanswered',
        'answer',
    ];

    public function __construct($uniqueid, \moodle_url $url) {
        parent::__construct($uniqueid);

        $this->set_attribute('class', 'generaltable generalbox');

        $this->make_columns();
        $systemcontext = \context_system::instance();
        $this->context = $systemcontext;
        $this->collapsible(false);
        $this->is_downloadable(true);
        $this->define_baseurl($url);
        $this->sortable(true, 'questionid');

        $this->set_sql(
            implode(',', self::COLUMNS),
            '{tool_beacon}',
            '1=1'
        );
        $this->column_class('type', 'text-nowrap');
        $this->column_class('timeanswered', 'text-nowrap');
    }

    /**
     * Defines the columns for this table.
     *
     * @throws \coding_exception
     */
    public function make_columns(): void {
        $headers = [];
        $columns = $this->get_columns();
        foreach ($columns as $column) {
            $headers[] = get_string('report:' . $column, 'tool_beacon');
        }

        $this->define_columns($columns);
        $this->define_headers($headers);
    }

    /**
     * returns the columns defined for the table.
     *
     * @return string[]
     */
    protected function get_columns(): array {
        $columns = self::COLUMNS;
        return $columns;
    }

    /**
     * Display value for 'timeanswered' column.
     *
     * @param object $record
     * @return string
     * @throws \coding_exception
     */
    public function col_timeanswered(object $record): string {
        $relativetime = get_time_interval_string($record->timeanswered, time());
        $relativetime = \html_writer::tag('span', "($relativetime)", ['class' => 'text-muted']);
        return userdate($record->timeanswered, get_string('strftime_datetime', 'tool_beacon')) .
            " $relativetime";
    }


    /**
     * For some questions, like check, it will return a nested array of results,
     * so recursion is probably expected.
     *
     * @param   array $answer
     * @return  string formatted answer as a table of results
     */
    private function format_answer(array $answer, $nested = false) {
        $data = [];
        foreach ($answer as $key => $value) {
            $row = [];

            // Apply preformatted text for the 'key'.
            if (!$nested) {
                $row[] = "<pre>$key</pre>";
            } else {
                $row[] = \html_writer::tag('div', $key, ['class' => 'font-weight-bold text-muted text-right']);
            }

            if (is_array($value)) {
                $row[] = $this->format_answer($value, true);
                $data[] = $row;
                continue;
            }

            // Get the string representation of the value.
            if (gettype($value) === 'boolean') {
                $basevalue = ($value ? 'true' : 'false');
            } else {
                $basevalue = strval($value);
            }
            $row[] = \html_writer::tag('div', gettype($value), [
                'class' => 'pr-2 text-muted small d-inline-block',
                'style' => 'width: 50px'
            ]) . $basevalue;

            $data[] = $row;
        }

        $table = new \html_table();
        $table->attributes['class'] = 'admintable generaltable table-sm';
        $table->data = $data;
        return \html_writer::table($table);
    }

    /**
     * Display value for 'answer' column.
     *
     * @param object $record
     * @return string
     * @throws \coding_exception
     */
    public function col_answer(object $record): string {
        if ($this->is_downloading()) {
            return $record->answer;
        }
        $answer = json_decode($record->answer, true);
        if (empty($answer)) {
            return '';
        }

        $response = $this->format_answer($answer);
        return $response;
    }
}
