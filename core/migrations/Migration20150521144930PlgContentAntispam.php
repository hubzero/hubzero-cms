<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding basic antispam plugin.
 *
 */
class Migration20150521144930PlgContentAntispam extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__extensions')) {
            $id = $this->db->getQuery(true)
                ->select('extension_id')
                ->from('#__extensions')
                ->where('folder', '=', 'content')
                ->where('element', '=', 'antispam')
                ->where('type', '=', 'plugin')
                ->value('extension_id');

            if (!$id) {
                $this->addPluginEntry('content', 'antispam');
            } else {
                // Set the first zone as default
                $this->db->getQuery(true)
                    ->update('#__extensions')
                    ->set(['state' => 0, 'name' => 'plg_content_antispam'])
                    ->where('extension_id', '=', $id)
                    ->execute();
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
            $id = $this->db->getQuery(true)
                ->select('extension_id')
                ->from('#__extensions')
                ->where('folder', '=', 'content')
                ->where('element', '=', 'antispam')
                ->where('type', '=', 'plugin')
                ->value('extension_id');

            if ($id) {
                $this->deletePluginEntry('content', 'antispam');
            }
        }
    }
}
