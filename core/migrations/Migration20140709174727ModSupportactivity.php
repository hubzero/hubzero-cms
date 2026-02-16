<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding mod_supportactivity
 *
 */
class Migration20140709174727ModSupportactivity extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        $element = 'mod_supportactivity';
        $params  = '';
        $enabled = 1;

        if ($schema->tableExists('#__extensions')) {
            $name = $element;

            // First, make sure it isn't already there
            $id = $this->db->getQuery(true)
                ->select('extension_id')
                ->from('#__extensions')
                ->where('name', '=', $name)
                ->value('extension_id');

            if ($id) {
                return true;
            }

            $ordering = 0;

            if (!empty($params) && is_array($params)) {
                $params = json_encode($params);
            }

            $this->db->getQuery(true)
                ->insert('#__extensions')
                ->set([
                    'name'              => $name,
                    'type'              => 'module',
                    'element'           => $element,
                    'folder'            => '',
                    'client_id'         => 1,
                    'enabled'           => $enabled,
                    'access'            => 1,
                    'protected'         => 0,
                    'manifest_cache'    => '',
                    'params'            => $params,
                    'custom_data'       => '',
                    'system_data'       => '',
                    'checked_out'       => 0,
                    'checked_out_time'  => '0000-00-00 00:00:00',
                    'ordering'          => $ordering,
                    'state'             => 0
                ])
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        $element = 'mod_supportactivity';

        if ($schema->tableExists('#__extensions')) {
            // Delete module entry
            $this->db->getQuery(true)
                ->delete('#__extensions')
                ->where('element', '=', $element)
                ->execute();
        }
    }
}
