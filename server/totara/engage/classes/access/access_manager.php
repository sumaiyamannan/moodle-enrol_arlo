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
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 * @package totara_engage
 */
namespace totara_engage\access;

use totara_engage\share\recipient\helper as recipient_helper;
use totara_engage\share\recipient\recipient;
use totara_engage\share\shareable;

final class access_manager {
    /**
     * Preventing the construction of this class
     * access_manager constructor.
     */
    private function __construct() {
    }

    /**
     * Check the if current user is able to admin things in Engage
     *
     * @param \context $context - Context in which to check capability
     * @param int $user_id - User to check capability (current user if 0)
     * @return bool
     */
    public static function can_manage_engage(\context $context, int $user_id = 0): bool {
        global $USER;
        if (!$user_id) {
            $user_id = $USER->id;
        }
        return has_capability('totara/engage:manage', $context, $user_id);
    }

    /**
     * Check against the totara/tenant:manageparticipants capability
     * to see if the user is a tenancy admin.
     *
     * @param int $user_id
     * @return bool
     */
    public static function can_manage_tenant_participants(int $user_id = 0): bool {
        global $USER;
        if (empty($user_id)) {
            $user_id = $USER->id;
        }

        $context = \context_system::instance();
        return has_capability('totara/tenant:manageparticipants', $context, $user_id);
    }

    /**
     * Checking whether the $user_id is able to access to the item or not.
     *
     * @param accessible $item
     * @param int|null $user_id
     * @return bool
     */
    public static function can_access(accessible $item, int $user_id = null): bool {
        global $USER, $CFG, $DB;
        if (empty($user_id)) {
            $user_id = $USER->id;
        }

        $ownerid = $item->get_userid();

        // Check to see if they're allowed to view the library
        $context = \context_user::instance($user_id);
        if (!has_capability('totara/engage:viewlibrary', $context, $user_id)) {
            return false;
        }

        if ($user_id == $ownerid) {
            // Same owner, so he/she can access to this very item.
            return true;
        }

        if (self::can_manage_engage(\context_user::instance($ownerid), $user_id)) {
            return true;
        }

        if ($item->is_private()) {
            return false;
        }

        if (!empty($CFG->tenantsenabled)) {
            $owner_tenant_id = $DB->get_field('user', 'tenantid', ['id' => $ownerid]);
            $actor_tenant_id = $DB->get_field('user', 'tenantid', ['id' => $user_id]);

            $sql = '
                SELECT 1 FROM "ttr_cohort_members" cm
                INNER JOIN "ttr_tenant" t ON t.cohortid = cm.cohortid
                WHERE cm.userid = :user_id
                AND t.id = :tenant_id
            ';

            if (null === $owner_tenant_id && null !== $actor_tenant_id) {
                // Actor is within a tenant, and owner is not. Therefore we have to check if these
                // users are within the same tenant or not.
                $in_same_tenant = $DB->record_exists_sql(
                    $sql,
                    [
                        'user_id' => $ownerid,
                        'tenant_id' => $actor_tenant_id
                    ]
                );

                if (!$in_same_tenant && !empty($CFG->tenantsisolated)) {
                    // Not in same tenant, and tenant isolation mode is on.
                    // Actor cannot see.
                    return false;
                }
            } else if (null !== $owner_tenant_id && null === $actor_tenant_id) {
                // Owner is within tenant, check if user actor is within same tenant or not.
                $in_same_tenant = $DB->record_exists_sql(
                    $sql,
                    [
                        'user_id' => $user_id,
                        'tenant_id' => $owner_tenant_id
                    ]
                );

                if (!$in_same_tenant) {
                    // System level user by default should not be able to see tenant's content.
                    // Until they are participant of the tenant.
                    return false;
                }
            } else if ($owner_tenant_id !== $actor_tenant_id) {
                // Both actor and owner either can be within a tenant or within the system level,
                // we just need to check if they are in the same tenant/system level or not.
                return false;
            }
        }

        if ($item->is_public()) {
            return true;
        }

        return self::has_shared_access($item, $user_id);
    }

    /**
     * Check if user is permitted via a share of this item.
     * Does not check for multi-tenancy
     *
     * @param accessible $item
     * @param int $user_id
     * @return bool
     */
    public static function has_shared_access(accessible $item, int $user_id): bool {
        /** @var recipient[] $recipients */
        $recipients = recipient_helper::get_recipient_classes();
        foreach ($recipients as $recipient) {
            if ($item instanceof shareable && $recipient::is_user_permitted($item, $user_id)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param int $old_access
     * @param int $new_access
     * @return bool
     */
    public static function can_update_access(int $old_access, int $new_access): bool {
        if (access::is_public($old_access) && !access::is_public($new_access)) {
            return false;
        }

        if (access::is_restricted($old_access) && access::is_private($new_access)) {
            return false;
        }

        return true;
    }

    /**
     * Checks whether the view user can view the library or not. Will trigger
     * a capability exception if not allowed.
     */
    public static function require_library_capability(): void {
        global $USER;

        // Check to see if they're allowed to view the library
        $context = \context_user::instance($USER->id);
        require_capability('totara/engage:viewlibrary', $context, $USER->id);
    }
}