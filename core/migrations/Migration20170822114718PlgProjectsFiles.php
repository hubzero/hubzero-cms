<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to update file handler base path
  *
**/
class Migration20170822114718PlgProjectsFiles extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__extensions')) {
            $row = $this->db->getQuery(true)
                ->select('*')
                ->from('#__extensions')
                ->where('folder', '=', 'projects')
                ->where('element', '=', 'files')
                ->first();

            if ($row && $row->params) {
                $params = json_decode($row->params);
                if ($params && isset($params->handler_base_path) && $params->handler_base_path != '/srv/projects/') {
                    $params->handler_base_path = rtrim($params->handler_base_path, '/') . '/{project}/{file}';

                    $this->db->getQuery(true)
                        ->update('#__extensions')
                        ->set(['params' => json_encode($params)])
                        ->where('extension_id', '=', $row->extension_id)
                        ->execute();
                }
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__extensions')) {
            $row = $this->db->getQuery(true)
                ->select('*')
                ->from('#__extensions')
                ->where('folder', '=', 'projects')
                ->where('element', '=', 'files')
                ->first();

            if ($row && $row->params) {
                $params = json_decode($row->params);
                if ($params && isset($params->handler_base_path) && strstr($params->handler_base_path, '{')) {
                    $params->handler_base_path = str_replace('{project}/{file}', '', $params->handler_base_path);

                    $this->db->getQuery(true)
                        ->update('#__extensions')
                        ->set(['params' => json_encode($params)])
                        ->where('extension_id', '=', $row->extension_id)
                        ->execute();
                }
            }
        }
    }
}
