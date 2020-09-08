<?php
/**
 * This file is part of Totara Learn
 *
 * Copyright (C) 2019 onwards Totara Learning Solutions LTD
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
 * @author  Johannes Cilliers <johannes.cilliers@totaralearning.com>
 * @package totara_engage
 */

namespace totara_engage\webapi\resolver\query;

use core\orm\query\builder;
use core\webapi\execution_context;
use core\webapi\middleware\require_advanced_feature;
use core\webapi\middleware\require_login;
use core\webapi\query_resolver;
use core\webapi\resolver\has_middleware;
use totara_engage\access\access;
use totara_engage\share\provider as share_provider;
use totara_engage\share\recipient\helper as recipient_helper;
use totara_engage\share\recipient\recipient;

/**
 * Resolver for finding destinations to where a user can share to.
 */
final class shareto_recipients implements query_resolver, has_middleware {

    /**
     * @param array $args
     * @param execution_context $ec
     * @return array
     */
    public static function resolve(array $args, execution_context $ec): array {
        global $USER;
        if (!$ec->has_relevant_context()) {
            $ec->set_relevant_context(\context_user::instance($USER->id));
        }

        $data = [];

        if (!empty($args['search'])) {
            $access = access::get_value($args['access']);
            $itemid = $args['itemid'];
            $component = $args['component'];

            // Get an instance of the item we are trying to share.
            $instance = null;
            if (!empty($itemid)) {
                $provider = share_provider::create($component);
                $instance = $provider->get_item_instance($itemid);
            }

            /** @var recipient[] $classes */
            $classes = recipient_helper::get_recipient_classes(true);

            // Execute search on each handler.
            foreach ($classes as $class) {
                /** @var recipient[] $recipients */
                $recipients = $class::search($args['search'], $instance);

                // Only user is explicitly specified as a return type in the graphql query
                // as we need the user to pass through the graphql user type resolver's
                // validations.
                $area = strtolower($class::AREA);
                if ($area !== 'user') {
                    $area = 'other';
                }

                // Get recipients that already received the share.
                $already_recipients = [];
                if (!empty($instance)) {
                    $already_recipients = self::get_already_recipients($itemid, $component, $recipients, $access);
                }

                // Setup recipient return data.
                foreach ($recipients as $recipient) {
                    // Allow only recipients that matches at least the item's access level.
                    if (!self::has_valid_access($recipient, $access)) {
                        continue;
                    }

                    $recipient_id = $recipient->get_id();
                    $recipient_area = $recipient->get_area();
                    $recipient_component = $recipient->get_component();

                    $isrecipient = false;
                    foreach ($already_recipients as $already_recipient) {
                        if ($already_recipient->instanceid == $recipient_id
                            && $already_recipient->area === $recipient_area
                            && $already_recipient->component === $recipient_component) {
                            $isrecipient = true;
                            break;
                        }
                    }

                    // Add to return data.
                    $data[] = [
                        'component' => $recipient_component,
                        'area' => $recipient_area,
                        'instanceid' => $recipient_id,
                        'alreadyshared' => $isrecipient,
                        'summary' => $recipient->get_summary(),
                        'minimum_access' => access::get_code($recipient->get_minimum_access()),
                        $area => $recipient->get_data()
                    ];
                }
            }
        }

        return $data;
    }

    /**
     * @param int $itemid
     * @param string $component
     * @param recipient[] $recipients
     * @param int $access
     * @param int|null $visibility
     * @return array
     */
    private static function get_already_recipients(int $itemid, string $component, array $recipients,
                                                   int $access, ?int $visibility = 1): array {
        // Setup builder
        $builder = builder::table('engage_share', 'es')
            ->join(['engage_share_recipient', 'esr'], 'esr.shareid', 'es.id')
            ->select([
                'esr.instanceid',
                'esr.area',
                'esr.component'
            ])
            ->where('es.itemid', $itemid)
            ->where('es.component', $component);

        $builder->where(function (builder $builder) use ($recipients, $access, $visibility) {
            // Flag recipients that are already recipients of the share.
            foreach ($recipients as $recipient) {
                // Allow only recipients that matches at least the item's access level.
                if (!self::has_valid_access($recipient, $access)) {
                    continue;
                }

                $builder->or_where(function (builder $builder) use ($recipient, $visibility) {
                    $builder->where('esr.instanceid', $recipient->get_id())
                        ->where('esr.area', $recipient->get_area())
                        ->where('esr.component', $recipient->get_component())
                        ->where('esr.visibility', $visibility);
                });
            }
        });

        return $builder->fetch();
    }

    /**
     * @param recipient $recipient
     * @param int $access
     * @return bool
     */
    private static function has_valid_access(recipient $recipient, int $access): bool {
        if (access::is_public($access) || $access === $recipient->get_minimum_access()) {
            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public static function get_middleware(): array {
        return [
            new require_login(),
            new require_advanced_feature('engage_resources'),
        ];
    }

}