<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding GeoSearch cron plugin.
 *
 */
class Migration20150722155100PlgCronGeosearch extends Base
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
                ->where('folder', '=', 'cron')
                ->where('element', '=', 'geosearch')
                ->where('type', '=', 'plugin');
            $id = $query->value('extension_id');

            if (!$id) {
                $this->addPluginEntry('cron', 'geosearch');
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
                ->where('folder', '=', 'cron')
                ->where('element', '=', 'geosearch')
                ->where('type', '=', 'plugin');
            $id = $query->value('extension_id');

            if ($id) {
                $this->deletePluginEntry('cron', 'geosearch');
            }
        }
    }
}
