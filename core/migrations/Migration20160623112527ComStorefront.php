<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to turn collections ID into auto increment field
 *
*/
class Migration20160623112527ComStorefront extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        if (
            $this->db->tableExists('#__storefront_collections')
            && $this->db->tableHasField('#__storefront_collections', 'cId')
        ) {
            $query = "ALTER TABLE `#__storefront_collections` CHANGE `cId` `cId` int(16) unsigned NOT NULL "
                . "AUTO_INCREMENT";
            $this->db->setQuery($query);
            $this->db->query();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        if (
            $this->db->tableExists('#__storefront_collections')
            && $this->db->tableHasField('#__storefront_collections', 'cId')
        ) {
            $query = "ALTER TABLE `#__storefront_collections` CHANGE `cId` `cId` char(50) NOT NULL AUTO_INCREMENT";
            $this->db->setQuery($query);
            $this->db->query();
        }
    }
}
