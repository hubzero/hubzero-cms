<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to rename production_collections primary key
 *
*/
class Migration20160623113106ComStorefront extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        if (
            $this->db->tableExists('#__storefront_product_collections')
            && $this->db->tableHasField('#__storefront_product_collections', 'cllId')
        ) {
            $query = "ALTER TABLE `#__storefront_product_collections` CHANGE `cllId` `pcId` int(16) NOT NULL "
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
            $this->db->tableExists('#__storefront_product_collections')
            && $this->db->tableHasField('#__storefront_product_collections', 'pcId')
        ) {
            $query = "ALTER TABLE `#__storefront_product_collections` CHANGE `pcId` `cllId` int(16) NOT NULL "
                . "AUTO_INCREMENT";
            $this->db->setQuery($query);
            $this->db->query();
        }
    }
}
