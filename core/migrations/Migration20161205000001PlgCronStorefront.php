<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Storefront cron plugin.
 *
*/
class Migration20161205000001PlgCronStorefront extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__extensions')) {
            $id = $this->db
                ->getQuery()
                ->select('extension_id')
                ->from('#__extensions')
                ->where('folder', '=', 'cron')
                ->where('element', '=', 'storefront')
                ->where('type', '=', 'plugin')
                ->value('extension_id');

            if (!$id) {
                $this->addPluginEntry('cron', 'storefront');
            }
        }
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__extensions')) {
            $id = $this->db
                ->getQuery()
                ->select('extension_id')
                ->from('#__extensions')
                ->where('folder', '=', 'cron')
                ->where('element', '=', 'storefront')
                ->where('type', '=', 'plugin')
                ->value('extension_id');

            if ($id) {
                $this->deletePluginEntry('cron', 'storefront');
            }
        }
    }
}
