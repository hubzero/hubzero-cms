<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to add AUTO_INCREMENT to pcId
 *
*/
class Migration20150924000002ComStorefront extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        // modifyColumn handles AUTO_INCREMENT appropriately for each database
        $this->db->schema()->modifyColumn('#__storefront_product_collections', 'pcId')
            ->integer()
            ->notNull()
            ->autoIncrement()
            ->execute();
    }
}
