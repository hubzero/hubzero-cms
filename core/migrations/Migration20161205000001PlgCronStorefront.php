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
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__extensions')) {
            $id = $this->db->getQuery(true)
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
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__extensions')) {
            $id = $this->db->getQuery(true)
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
