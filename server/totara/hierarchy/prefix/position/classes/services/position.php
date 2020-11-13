<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2018 onwards Totara Learning Solutions LTD
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Aleksandr Baishev <aleksandr.baishev@totaralearning.com>
 * @package hierarchy_position
 */

namespace hierarchy_position\services;

use context_system;
use core\format;
use core\orm\paginator;
use core\orm\query\builder;
use external_function_parameters;
use external_multiple_structure;
use external_single_structure;
use external_value;
use hierarchy_position\entity\position as position_entity;
use totara_core\advanced_feature;
use core\webapi\formatter\field\string_field_formatter;
use core\webapi\formatter\field\text_field_formatter;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/lib/externallib.php');

class position extends \external_api {

    /**
     * @return external_function_parameters
     */
    public static function index_parameters(): external_function_parameters {
        return new external_function_parameters(
            [
                'filters' => new external_single_structure(
                    [
                        'text' => new external_value(PARAM_TEXT, 'Text search field value', VALUE_OPTIONAL, null),
                        'framework' => new external_value(PARAM_INT, 'Position framework id', VALUE_OPTIONAL, null),
                        'parent' => new external_value(PARAM_INT, 'Filter children (one level only)', VALUE_OPTIONAL, null),
                        'path' => new external_value(PARAM_INT, 'Filter children (all levels below)', VALUE_OPTIONAL, null),
                        'basket' => new external_value(PARAM_ALPHANUMEXT, 'Search by basket key', VALUE_OPTIONAL, null),
                        'visible' => new external_value(PARAM_BOOL, 'Filter by visible', VALUE_OPTIONAL, true),
                        'type' => new external_multiple_structure(
                            new external_value(PARAM_INT, 'id of the type', VALUE_OPTIONAL),
                            'one or more position type ids',
                            VALUE_OPTIONAL
                        ),
                        'ids' => new external_multiple_structure(
                            new external_value(PARAM_INT, 'ids', VALUE_OPTIONAL),
                            'ids to filter by',
                            VALUE_OPTIONAL
                        ),
                    ],
                    VALUE_REQUIRED
                ),
                'page' => new external_value(PARAM_INT, 'Pagination: page to load', VALUE_REQUIRED),
                'order' => new external_value(PARAM_ALPHANUMEXT, 'Name of column to order by - not used currently', VALUE_REQUIRED),
                'direction' => new external_value(PARAM_ALPHA, 'either ASC or DESC - not used currently', VALUE_REQUIRED),
            ]
        );
    }

    /**
     * @param array $filters
     * @param int $page
     * @param string $order
     * @param string $direction
     * @return array
     */
    public static function index(array $filters, int $page, string $order, string $direction) {
        advanced_feature::require('positions');

        if (!has_capability('totara/hierarchy:viewpositionframeworks', context_system::instance())
            || !has_capability('totara/hierarchy:viewposition', context_system::instance())
        ) {
            return paginator::new(builder::table(position_entity::TABLE)->where_raw('1 = 0'))->to_array();
        }

        if (!array_key_exists('visible', $filters)) {
            $filters['visible'] = true;
        }

        return position_entity::repository()
            ->reset_select()
            ->select_only_fields_for_picker()
            ->with_children_count()
            ->set_filters($filters)
            ->order_by($order, $direction)
            ->paginate($page)
            ->transform(function (position_entity $item) {
                $name = format_string($item->fullname);
                return [
                    'id' => $item->id,
                    'fullname' => $name,
                    'display_name' => $name,
                    'idnumber' => $item->idnumber,
                    'crumbtrail' => $item->crumbtrail
                ];
            })->to_array();
    }
    /**
     * @return null
     */
    public static function index_returns() {
        return null;
    }

    /**
     * @return external_function_parameters
     */
    public static function show_parameters(): external_function_parameters {
        return new external_function_parameters(
            [
                'id' => new external_value(PARAM_INT, 'id', VALUE_REQUIRED),
                'include' => new external_single_structure(
                    [
                        'crumbs' => new external_value(PARAM_INT, 'with_crumbtrail', VALUE_OPTIONAL, 0),
                    ],
                    'options'
                )
            ]
        );
    }

    /**
     * Returns one entity
     *
     * @param int $id
     * @param array $options
     * @return array
     */
    public static function show(int $id, array $options) {
        advanced_feature::require('positions');
        $context = context_system::instance();
        require_capability('totara/hierarchy:viewposition', $context);

        /** @var position_entity $item */
        $item = position_entity::repository()->find($id);
        if (empty($item)) {
            return [];
        }

        $string_formatter = new string_field_formatter(format::FORMAT_HTML, $context);
        $text_formatter = (new text_field_formatter(format::FORMAT_HTML, $context))
            ->set_pluginfile_url_options($context, 'totara_hierarchy', 'pos', $id);

        $position = [
            'id' => $item->id,
            'fullname' => $string_formatter->format($item->fullname),
            'idnumber' => $item->idnumber,
            'description' => $text_formatter->format($item->description),
            'shortname' => $string_formatter->format($item->shortname),
            'visible' => $item->visible,
            'frameworkid' => $item->frameworkid
        ];

        if (!empty($options['crumbs'])) {
            $position['crumbtrail'] = $item->crumbtrail;
        }

        return $position;
    }

    /**
     * @return null
     */
    public static function show_returns() {
        return null;
    }

}