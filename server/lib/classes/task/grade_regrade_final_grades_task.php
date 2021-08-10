<?php
/**
 * This file is part of Totara Core
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
 * @author  Kian Nguyen <kian.nguyen@totaralearning.com>
 * @package core
 */
namespace core\task;

/**
 * An adhoc task that is queued when we would want to regrade the final
 * grades for a course.
 */
class grade_regrade_final_grades_task extends adhoc_task {
    /**
     * @param int      $course_id
     * @return grade_regrade_final_grades_task
     */
    public static function enqueue(int $course_id): grade_regrade_final_grades_task {
        $task = new grade_regrade_final_grades_task();
        $task->set_custom_data(['course_id' => $course_id]);

        manager::queue_adhoc_task($task);
        return $task;
    }

    /**
     * @return void
     */
    public function execute(): void {
        global $CFG;
        require_once("{$CFG->dirroot}/lib/gradelib.php");

        $data = $this->get_custom_data();
        grade_regrade_final_grades($data->course_id);
    }
}