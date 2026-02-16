<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding 'member home page' system plugin
  *
**/
class Migration20150813115100PlgSystemMemberhome extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__extensions')) {
            $query = $this->db->getQuery(true)
                ->select('extension_id')
                ->from('#__extensions')
                ->where('folder', '=', 'system')
                ->where('element', '=', 'memberhome')
                ->where('type', '=', 'plugin');
            $id = $query->value('extension_id');

            if (!$id) {
                $this->addPluginEntry('system', 'memberhome', 0);
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
            $query = $this->db->getQuery(true)
                ->select('extension_id')
                ->from('#__extensions')
                ->where('folder', '=', 'system')
                ->where('element', '=', 'memberhome')
                ->where('type', '=', 'plugin');
            $id = $query->value('extension_id');

            if ($id) {
                $this->deletePluginEntry('system', 'memberhome');
            }
        }
    }
}
