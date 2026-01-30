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
        if ($this->db->tableExists('#__extensions')) {
            $query = "SELECT `extension_id` FROM `#__extensions` WHERE `folder` = 'cron' AND `element` ="
                . "'storefront' AND `type` = 'plugin'";
            $this->db->setQuery($query);
            $id = $this->db->loadResult();

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
        if ($this->db->tableExists('#__extensions')) {
            $query = "SELECT `extension_id` FROM `#__extensions` WHERE `folder` = 'cron' AND `element` ="
                . "'storefront' AND `type` = 'plugin'";
            $this->db->setQuery($query);
            $id = $this->db->loadResult();

            if ($id) {
                $this->deletePluginEntry('cron', 'storefront');
            }
        }
    }
}
