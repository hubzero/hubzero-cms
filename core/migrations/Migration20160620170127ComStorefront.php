<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to add access column to products table
**/
class Migration20160620170127ComStorefront extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        if (
            $this->db->tableExists('#__storefront_products')
            && !$this->db->tableHasField('#__storefront_products', 'access')
        ) {
            $query = "ALTER TABLE `#__storefront_products` ADD `access` tinyint(3) NOT NULL DEFAULT '0'";
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
            $this->db->tableExists('#__storefront_products')
            && $this->db->tableHasField('#__storefront_products', 'access')
        ) {
            $query = "ALTER TABLE `#__storefront_products` DROP COLUMN `access`";
            $this->db->setQuery($query);
            $this->db->query();
        }
    }
}
