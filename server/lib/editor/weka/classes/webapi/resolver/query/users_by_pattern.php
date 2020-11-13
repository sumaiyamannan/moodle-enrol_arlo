<?php
/**
 * This file is part of Totara Learn
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTD
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
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 * @package editor_weka
 */
namespace editor_weka\webapi\resolver\query;

use core\entity\user_repository;
use core\webapi\execution_context;
use core\webapi\middleware\require_login;
use core\webapi\query_resolver;
use core\webapi\resolver\has_middleware;
use context_user;
use context;
use core\entities\user;
use editor_weka\hook\search_users_by_pattern;

/**
 * Searching users by pattern.
 */
final class users_by_pattern implements query_resolver, has_middleware {
    /**
     * @param array $args
     * @param execution_context $ec
     *
     * @return user[]
     */
    public static function resolve(array $args, execution_context $ec): array {
        global $USER;

        // Fallback to the current user's in session. Note that we are not using system context here,
        // because context user can define who this user can see  and so on. Moreover, it is quite safe
        // to use context_user, user has to exist in the system in order to execute this query.
        $context = context_user::instance($USER->id);
        if (isset($args['contextid'])) {
            $context = context::instance_by_id($args['contextid']);
        }

        if (!$ec->has_relevant_context()) {
            $ec->set_relevant_context($context);
        }

        if ($context->is_user_access_prevented($USER->id)) {
            throw new \coding_exception("User with id '{$USER->id}' cannot access context");
        }

        $pattern = $args['pattern'] ?? '';

        if (!empty($args['component']) && !empty($args['area']) && !empty($args['contextid'])) {
            $hook = search_users_by_pattern::create(
                $args['component'],
                $args['area'],
                $pattern,
                $args['contextid']
            );

            if (isset($args['instance_id'])) {
                $hook->set_instance_id($args['instance_id']);
            }

            $hook->execute();

            if ($hook->is_db_run()) {
                // Hook has run against the database, hence we will just return whatever had been added
                // to the hook.
                return $hook->get_users();
            }
        }

        return user_repository::search($context, $pattern, 20)->all();
    }

    /**
     * @inheritDoc
     */
    public static function get_middleware(): array {
        return [
            new require_login(),
        ];
    }
}