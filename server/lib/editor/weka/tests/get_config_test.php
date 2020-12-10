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
defined('MOODLE_INTERNAL') || die();

use editor_weka\config\factory;

class editor_weka_get_config_testcase extends advanced_testcase {
    /**
     * Test to assure that our cache is able to be constructed.
     * @return void
     */
    public function test_get_empty_config(): void {
        $factory = new factory();

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage("Cannot find the configuration of area 'something_that_is_not_existing'");

        $factory->get_configuration('editor_weka', 'something_that_is_not_existing');
    }
}