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
 * @author Jaron Steenson <jaron.steenson@totaralearning.com>
 * @package mod_perform
 * @category test
 */

use core\collection;
use mod_perform\constants;
use mod_perform\entity\activity\subject_instance as subject_instance_entity;
use mod_perform\entity\activity\participant_instance as participant_instance_entity;
use mod_perform\models\activity\subject_instance;
use mod_perform\state\subject_instance\active as subject_active;
use mod_perform\state\subject_instance\pending as subject_pending;
use mod_perform\testing\generator;
use totara_core\relationship\relationship;

/**
 * @group perform
 */
class mod_perform_subject_instance_model_testcase extends advanced_testcase {

    /**
     * @param int $extra_instance_count
     * @dataProvider get_instance_count_provider
     */
    public function test_get_instance_count(int $extra_instance_count): void {
        $this->setAdminUser();

        /** @var mod_perform_generator $perform_generator */
        $perform_generator = self::getDataGenerator()->get_plugin_generator('mod_perform');

        $config = mod_perform_activity_generator_configuration::new()
            ->set_number_of_activities(1)
            ->set_number_of_tracks_per_activity(1)
            ->set_number_of_users_per_user_group_type(1);

        $perform_generator->create_full_activities($config)->first();

        /** @var subject_instance_entity $subject_instance_entity */
        $subject_instance_entity = subject_instance_entity::repository()->order_by('id')->first();

        $i = 0;
        $now = time();
        while ($extra_instance_count > $i) {
            $extra_subject_instance = new subject_instance_entity();
            $extra_subject_instance->track_user_assignment_id = $subject_instance_entity->track_user_assignment_id;
            $extra_subject_instance->subject_user_id = $subject_instance_entity->subject_user_id;
            $extra_subject_instance->created_at = $now + ($i + 1); // Force a decent gap between created at times.
            $extra_subject_instance->save();

            $i++;
        }

        $last_instance_entity = $extra_subject_instance ?? $subject_instance_entity;

        $first_instance_count = (new subject_instance($subject_instance_entity))->get_instance_count();
        $last_instance_count = (new subject_instance($last_instance_entity))->get_instance_count();

        self::assertEquals(1, $first_instance_count);
        self::assertEquals($extra_instance_count + 1, $last_instance_count);
    }

    public function get_instance_count_provider(): array {
        return [
            'Single' => [0],
            'Double' => [1],
            'Triple' => [2],
        ];
    }

    /**
     * Test to delete participant instance, participant sections and section element responses.
     */
    public function test_manually_delete(): void {

        [$activity, $subject_instances] = $this->create_test_data();

        /** @var subject_instance $subject_instance */
        $subject_instance = subject_instance::load_by_entity(
            subject_instance_entity::repository()->get()->first()
        );

        $subject_instance_id = $subject_instance->id;

        $subject_instance->manually_delete();

        foreach ($subject_instances as $si) {
            $subject_instance_entity = subject_instance_entity::repository()->find($si->id);
            $participant_instances = $si->get_participant_instances();
            if ($si->id == $subject_instance_id) { // The one we deleted.
                $this->assertNull($subject_instance_entity);
                $this->assertEquals(0, $participant_instances->count());
            } else {
                $this->assertNotNull($subject_instance_entity->id);
                foreach ($participant_instances as $pi) {
                    $participant_instance_entity = participant_instance_entity::repository()->find($pi->get_id());
                    $this->assertNotNull($participant_instance_entity);
                }
            }
        }
    }

    private function create_test_data(
        int $no_of_subject_instances = 5,
        bool $subject_instance_active = true
    ): array {
        $this->setAdminUser();

        $perform_generator = self::getDataGenerator()->get_plugin_generator('mod_perform');
        $activity = $perform_generator->create_activity_in_container();

        $core_generator = $this->getDataGenerator();
        $si_data = [
            'activity_id' => $activity->id,
            'other_participant_id' => $core_generator->create_user()->id,
            'subject_is_participating' => true,
            'include_questions' => false,
            'status' => $subject_instance_active ? subject_active::get_code() :subject_pending::get_code()
        ];

        $subject_instances = collection::new(range(1, $no_of_subject_instances))
            ->map_to(
                function (int $i) use ($core_generator): int {
                    return $core_generator->create_user()->id;
                }
            )
            ->map_to(
                function (int $uid) use ($si_data, $perform_generator): subject_instance {
                    $data = array_merge(['subject_user_id' => $uid], $si_data);
                    $entity = $perform_generator->create_subject_instance($data);

                    return subject_instance::load_by_entity($entity);
                }
            );

        return [$activity, $subject_instances];
    }

    public function test_manually_close_pending_throws_exception(): void {
        $subject_instance = $this->create_pending_subject_instance();

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('Cannot close a pending subject instance');
        $subject_instance->manually_close();
    }

    public function test_manually_close_pending_successful(): void {
        $subject_instance = $this->create_pending_subject_instance();

        $subject_instance->manually_close(true);

        // Reload model.
        $subject_instance = $subject_instance::load_by_id($subject_instance->id);

        self::assertTrue($subject_instance->is_closed());
        self::assertTrue($subject_instance->is_pending());
    }

    /**
     * @return subject_instance
     */
    private function create_pending_subject_instance(): subject_instance {
        self::setAdminUser();
        $user = self::getDataGenerator()->create_user();
        $perform_generator = self::getDataGenerator()->get_plugin_generator('mod_perform');

        $subject_relationship = relationship::load_by_idnumber(constants::RELATIONSHIP_SUBJECT);
        $peer_relationship = relationship::load_by_idnumber(constants::RELATIONSHIP_PEER);

        $activity = $perform_generator->create_activity_in_container();
        $perform_generator->create_manual_relationships_for_activity($activity, [
            ['selector' => $subject_relationship->id, 'manual' => $peer_relationship->id],
        ]);

        $subject_instance_entity = $perform_generator->create_subject_instance_with_pending_selections(
            $activity, $user, [$peer_relationship]
        );
        $subject_instance = subject_instance::load_by_entity($subject_instance_entity);
        self::assertTrue($subject_instance->is_pending());
        self::assertTrue($subject_instance->is_open());

        return $subject_instance;
    }
}
