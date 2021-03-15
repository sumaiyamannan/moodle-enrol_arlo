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

require_once(__DIR__ . '/../../../config.php');
require_once('../setup.php');
require_once('../lib.php');

$download = optional_param('download', '', PARAM_RAW);
if ($download) {
    header('Content-Disposition: attachment; filename=' . $catadminsaml->spname . '.xml');
}

$regenerate = is_siteadmin() && optional_param('regenerate', false, PARAM_BOOL);
if ($regenerate) {
    $file = $catadminsaml->get_file_sp_metadata_file();
    @unlink($file);
}

$xml = auth_catadmin_get_sp_metadata();

if (array_key_exists('output', $_REQUEST) && $_REQUEST['output'] == 'xhtml') {

    $t = new SimpleSAML_XHTML_Template($config, 'metadata.php', 'admin');

    $t->data['header'] = 'saml20-sp';
    $t->data['metadata'] = htmlspecialchars($xml);
    $t->data['metadataflat'] = '$metadata[' . var_export($entityId, true) . '] = ' . var_export($metaArray20, true) . ';';
    $t->data['metaurl'] = $source->getMetadataURL();
    $t->show();
} else {
    header('Content-Type: text/xml');
    echo($xml);
}
