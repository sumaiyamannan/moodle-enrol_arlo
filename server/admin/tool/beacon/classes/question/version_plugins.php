<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * VERSION_PLUGINS question type.
 *
 * @package     tool_beacon
 * @copyright   2020 Nicholas Hoobin <nicholashoobin@catalyst-au.net>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_beacon\question;

use tool_beacon\model\beacon_row_kv;

class version_plugins extends question {

    private function get_contrib_plugins() {
        $pluginman = \core_plugin_manager::instance();
        $plugininfo = $pluginman->get_plugins();

        $contribs = [];
        foreach ($plugininfo as $plugintype => $pluginnames) {
            foreach ($pluginnames as $pluginname => $pluginfo) {
                if (!$pluginfo->is_standard()) {
                    $contribs[$plugintype][$pluginname] = $pluginfo;
                }
            }
        }

        return $contribs;
    }

    private function get_plugin_update_information($plugin) {
        $updateinfo = [];

        // Case for Totara, Error : Call to undefined method editor_atto\plugininfo\atto::available_updates().
        if (!method_exists($plugin, 'available_updates')) {
            return '';
        }

        if (is_array($plugin->available_updates())) {
            foreach ($plugin->available_updates() as $availableupdate) {
                $updateinfo[] = $availableupdate->version;
            }
        }
        return implode(',', $updateinfo);
    }

    protected function query() {
        global $CFG;

        $plugininfo = $this->get_contrib_plugins();

        foreach ($plugininfo as $plugins) {
            foreach ($plugins as $plugin) {

                // Strips the dir root to obtain the plugin path, eg 'admin/tool/beacon'.
                $path = str_replace($CFG->dirroot . '/', '', $plugin->rootdir);

                $values = [
                    'path' => $path,
                    'version' => $plugin->versiondb,
                    'release' => $plugin->release,
                    'requires' => $plugin->versionrequires,
                    'component' => $plugin->component,
                    'update' => $this->get_plugin_update_information($plugin)
                ];

                foreach ($values as $key => $value) {
                    $returndata[] = new beacon_row_kv(
                        $this->domain,
                        $this->timestamp,
                        $this->type,
                        $this->questionid,
                        $plugin->component,
                        $key,
                        $value
                    );
                }
            }
        }

        return $returndata;
    }
}
