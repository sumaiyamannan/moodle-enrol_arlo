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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Tatsuhiro Kirihara <tatsuhiro.kirihara@totaralearning.com>
 * @package mod_perform
 * @category test
 */

use mod_perform\constants;
use mod_perform\models\activity\notification;
use mod_perform\models\activity\notification_recipient;

require_once(__DIR__ . '/notification_testcase.php');

/**
 * @covers mod_perform\models\activity\notification_recipient
 * @group perform
 */
class mod_perform_notification_recipient_model_testcase extends mod_perform_notification_testcase {
    public function test_create_standard() {
        $activity = $this->create_activity();
        $section = $this->create_section($activity);
        $notification = notification::create($activity, 'instance_created');
        $relationships = $this->create_section_relationships($section);
        notification_recipient::create($notification, $relationships[constants::RELATIONSHIP_SUBJECT], false);
        notification_recipient::create($notification, $relationships[constants::RELATIONSHIP_APPRAISER], true);
        $this->assertFalse($notification->recipients->find('relationship_id', $relationships[constants::RELATIONSHIP_SUBJECT]->id)->active);
        $this->assertTrue($notification->recipients->find('relationship_id', $relationships[constants::RELATIONSHIP_APPRAISER]->id)->active);
        $this->assertFalse($notification->recipients->find('relationship_id', $relationships[constants::RELATIONSHIP_MANAGER]->id)->active);
    }

    public function test_create_manual() {
        $activity = $this->create_activity();
        $section = $this->create_section($activity);
        $notification = notification::create($activity, 'participant_selection');
        $manuals = [constants::RELATIONSHIP_PEER, constants::RELATIONSHIP_MENTOR, constants::RELATIONSHIP_REVIEWER];
        $relationships = $this->create_section_relationships($section, array_merge($manuals, $this->get_default_relationships_for_testing()));
        foreach ($manuals as $idnumber) {
            try {
                notification_recipient::create($notification, $relationships[$idnumber]);
                $this->fail('invalid_parameter_exception expected');
            } catch (invalid_parameter_exception $ex) {
                $this->assertStringContainsString($idnumber . ' is unavailable', $ex->getMessage());
            }
        }

        $notification = notification::create($activity, 'instance_created_reminder');
        $manuals = [constants::RELATIONSHIP_PEER, constants::RELATIONSHIP_MENTOR, constants::RELATIONSHIP_REVIEWER];
        $relationships = $this->create_section_relationships($section, array_merge($manuals, $this->get_default_relationships_for_testing()));
        notification_recipient::create($notification, $relationships[constants::RELATIONSHIP_PEER]);
        notification_recipient::create($notification, $relationships[constants::RELATIONSHIP_MENTOR], true);
        notification_recipient::create($notification, $relationships[constants::RELATIONSHIP_REVIEWER], false);
        $this->assertFalse($notification->recipients->find('relationship_id', $relationships[constants::RELATIONSHIP_PEER]->id)->active);
        $this->assertTrue($notification->recipients->find('relationship_id', $relationships[constants::RELATIONSHIP_MENTOR]->id)->active);
        $this->assertFalse($notification->recipients->find('relationship_id', $relationships[constants::RELATIONSHIP_REVIEWER]->id)->active);
    }

    public function test_load_by_notification_full() {
        $activity = $this->create_activity();
        $section = $this->create_section($activity);
        $notification = notification::create($activity, 'participant_selection');
        $manuals = [constants::RELATIONSHIP_PEER, constants::RELATIONSHIP_MENTOR, constants::RELATIONSHIP_REVIEWER];
        $relationships = $this->create_section_relationships($section, array_merge($manuals, $this->get_default_relationships_for_testing()));
        notification_recipient::create($notification, $relationships[constants::RELATIONSHIP_SUBJECT], false);
        notification_recipient::create($notification, $relationships[constants::RELATIONSHIP_APPRAISER], true);

        $this->assertCount(4, notification_recipient::load_by_notification($notification, false));
        $this->assertCount(1, notification_recipient::load_by_notification($notification, true));
    }

    public function test_load_by_notification_partial() {
        $activity = $this->create_activity();
        $section = $this->create_section($activity);
        $notification = notification::create($activity, 'participant_selection');
        $relationships = $this->create_section_relationships(
            $section,
            [constants::RELATIONSHIP_SUBJECT, constants::RELATIONSHIP_APPRAISER, constants::RELATIONSHIP_PEER]
        );
        notification_recipient::create($notification, $relationships[constants::RELATIONSHIP_SUBJECT], false);
        notification_recipient::create($notification, $this->get_core_relationship(constants::RELATIONSHIP_MANAGER), true);
        $this->assertCount(4, notification_recipient::load_by_notification($notification, false));
        $this->assertCount(1, notification_recipient::load_by_notification($notification, true));

        $activity = $this->create_activity();
        $section = $this->create_section($activity);
        $notification = notification::create($activity, 'completion');
        $relationships = $this->create_section_relationships(
            $section,
            [constants::RELATIONSHIP_SUBJECT, constants::RELATIONSHIP_EXTERNAL]
        );
        notification_recipient::create($notification, $relationships[constants::RELATIONSHIP_SUBJECT], false);
        $this->assertCount(1, notification_recipient::load_by_notification($notification, false));
        $this->assertCount(0, notification_recipient::load_by_notification($notification, true));
    }
}
