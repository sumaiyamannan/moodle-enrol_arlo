<?php
/**
 * This file is part of Totara Learn
 *
 * Copyright (C) 2022 onwards Totara Learning Solutions LTD
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
 * @author Oleg Demeshev <oleg.demeshev@totaralearning.com>
 * @package mod_perform
 */

use core\collection;
use mod_perform\testing\generator as perform_generator;
use mod_perform\models\activity\section_element;
use mod_perform\models\activity\participant_instance;
use mod_perform\models\response\section_element_response;
use mod_perform\entity\activity\element_response as element_response_entity;
use mod_perform\entity\activity\section_element as section_element_entity;
use mod_perform\entity\activity\participant_section as participant_section_entity;
use mod_perform\entity\activity\participant_instance as participant_instance_entity;

/**
 * @group perform
 */
class mod_perform_participant_instance_testcase extends advanced_testcase {

    /**
     * Test to delete participant instance, participant sections and section element responses.
     */
    public function test_manually_delete(): void {

        self::setAdminUser();
        $generator = self::getDataGenerator();

        $perform_generator = $this->getDataGenerator()->get_plugin_generator('mod_perform');
        $activity = $perform_generator->create_activity_in_container();

        $subject_user = $generator->create_user();
        $participant = $generator->create_user();

        $subject_instance = $perform_generator->create_subject_instance([
            'activity_id' => $activity->id,
            'subject_is_participating' => true,
            'subject_user_id' => $subject_user->id,
            'other_participant_id' => $participant->id,
            'include_questions' => true,
        ]);

        /** @var  section_element_entity $section_element */
        $section_element = section_element_entity::repository()->get()->first();
        /** @var participant_instance_entity $participant_instance */
        $participant_instance_entity = $subject_instance->participant_instances->first();
        $other_participant_instance_entity = $subject_instance->participant_instances->last();
        $this->assertNotNull($participant_instance_entity->id);
        $participant_instance_id = $participant_instance_entity->id;
        /** @var participant_instance $participant_instance */
        $participant_instance = participant_instance::load_by_entity($participant_instance_entity);
        $element_response = new section_element_response(
            $participant_instance,
            section_element::load_by_entity($section_element),
            null,
            new collection()
        );

        /** @var collection $participant_sections */
        $participant_sections = $participant_instance->get_participant_sections();
        $this->assertNotNull($participant_sections);
        $this->assertCount(1, $participant_sections);

        $element_response->set_response_data(json_encode('Hooooray'));
        $element_response->save();
        $element_response_entity = new element_response_entity($element_response->id);
        $this->assertNotNull($element_response_entity);

        $this->assertEquals($participant_instance->id, $element_response_entity->participant_instance_id);
        $this->assertEquals($section_element->id, $element_response_entity->section_element_id);

        $participant_instance->manually_delete();

        $this->assertNull(element_response_entity::repository()->find($element_response_entity->id));
        foreach ($participant_sections as $participant_section) {
            $this->assertNull(participant_section_entity::repository()->find($participant_section->id));
        }
        $this->assertNull(participant_instance_entity::repository()->find($participant_instance_id));
        $this->assertNotNull(participant_instance_entity::repository()->find($other_participant_instance_entity->id));
    }
}
