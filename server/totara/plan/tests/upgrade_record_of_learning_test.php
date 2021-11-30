<?php
/**
 * This file is part of Totara Learn
 *
 * Copyright (C) 2021 onwards Totara Learning Solutions LTD
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
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @package totara_plan
 */

use core\entity\user_enrolment;
use totara_plan\entity\record_of_learning as record_of_learning_entity;
use totara_plan\record_of_learning;

defined('MOODLE_INTERNAL') || die();

/**
 * Tests for upgrading and migrating the record of learning
 */
class totara_plan_upgrade_record_of_learning_testcase extends advanced_testcase {

    public function test_upgrade() {
        global $DB, $CFG;

        require_once($CFG->dirroot . '/totara/plan/db/upgradelib.php');

        $generator = $this->getDataGenerator();

        /* @var core_completion_generator $completion_generator */
        $completion_generator = $generator->get_plugin_generator('core_completion');

        /** @var totara_plan_generator $plan_generator */
        $plan_generator = $generator->get_plugin_generator('totara_plan');

        $user1 = $generator->create_user();
        $user2 = $generator->create_user();
        $user3 = $generator->create_user();

        $course1 = $generator->create_course();
        $course2 = $generator->create_course();
        $course3 = $generator->create_course();

        $completion_generator->enable_completion_tracking($course2);

        // Enrolling a user should result in a record in the record of learning
        $generator->enrol_user($user1->id, $course1->id);

        // Now make sure the user has a completion record
        // but is not enrolled anymore. A completion record should
        // result in a record in the record of learning
        $completion_generator->complete_course($course2, $user2);

        // Now assign a course to the learning plan of user 3 which
        // should result in a record in the record of learning
        $this->assertFalse(user_enrolment::repository()->where('userid', $user2->id)->exists());
        $plan1 = $plan_generator->create_learning_plan(['userid' => $user3->id]);

        $this->setUser($user3);
        $plan_generator->add_learning_plan_course($plan1->id, $course3->id);

        $this->setAdminUser();

        $manager = $DB->get_manager();

        // Let's drop the table to be able to test whether it gets correctly created
        $table = new xmldb_table('dp_record_of_learning');
        $manager->drop_table($table);

        // Now run the upgrade code
        totara_plan_upgrade_record_of_learning();

        // Make sure all records are there in the new table
        $this->assertEquals(3, record_of_learning_entity::repository()->count());

        $user1_record = record_of_learning_entity::repository()
            ->where('userid', $user1->id)
            ->one();

        $this->assertEquals($course1->id, $user1_record->instanceid);
        $this->assertEquals(record_of_learning::TYPE_COURSE, $user1_record->type);

        $user2_record = record_of_learning_entity::repository()
            ->where('userid', $user2->id)
            ->one();

        $this->assertEquals($course2->id, $user2_record->instanceid);
        $this->assertEquals(record_of_learning::TYPE_COURSE, $user2_record->type);

        $user3_record = record_of_learning_entity::repository()
            ->where('userid', $user3->id)
            ->one();

        $this->assertEquals($course3->id, $user3_record->instanceid);
        $this->assertEquals(record_of_learning::TYPE_COURSE, $user3_record->type);

        // Now run the upgrade code a SECOND time to make sure it does not fail
        totara_plan_upgrade_record_of_learning();

        // Make sure all records are there in the new table
        $this->assertEquals(3, record_of_learning_entity::repository()->count());

        $user1_record = record_of_learning_entity::repository()
            ->where('userid', $user1->id)
            ->one();

        $this->assertEquals($course1->id, $user1_record->instanceid);
        $this->assertEquals(record_of_learning::TYPE_COURSE, $user1_record->type);

        $user2_record = record_of_learning_entity::repository()
            ->where('userid', $user2->id)
            ->one();

        $this->assertEquals($course2->id, $user2_record->instanceid);
        $this->assertEquals(record_of_learning::TYPE_COURSE, $user2_record->type);

        $user3_record = record_of_learning_entity::repository()
            ->where('userid', $user3->id)
            ->one();

        $this->assertEquals($course3->id, $user3_record->instanceid);
        $this->assertEquals(record_of_learning::TYPE_COURSE, $user3_record->type);
    }
}
