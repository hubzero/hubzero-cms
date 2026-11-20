<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
     *
     * @return  void
     **/
    public function up()
    {
        $schema = $this->db->schema();

        // renameColumn handles AUTO_INCREMENT appropriately for each database
        if (
            $schema->tableExists('#__storefront_product_collections')
            && $schema->hasColumn('#__storefront_product_collections', 'cllId')
        ) {
            $schema
                ->renameColumn('#__storefront_product_collections', 'cllId', 'pcId')
                ->integer(16)
                ->notNull()
                ->autoIncrement()
                ->execute();
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

        // renameColumn handles AUTO_INCREMENT appropriately for each database
        if (
            $schema->tableExists('#__storefront_product_collections')
            && $schema->hasColumn('#__storefront_product_collections', 'pcId')
        ) {
            $schema
                ->renameColumn('#__storefront_product_collections', 'pcId', 'cllId')
                ->integer(16)
                ->notNull()
                ->autoIncrement()
                ->execute();
        }
    }
}
