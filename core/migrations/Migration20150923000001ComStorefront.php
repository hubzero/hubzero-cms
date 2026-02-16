<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to create the downloads log table
  *
**/
class Migration20150923000001ComStorefront extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__storefront_collections')
            && !$schema->hasColumn('#__storefront_collections', 'cAlias')
        ) {
            $schema->addColumn('#__storefront_collections', 'cAlias')->char(50)->nullable()->default(null)->execute();
        }

        // modifyColumn handles AUTO_INCREMENT appropriately for each database
        $schema->modifyColumn('#__storefront_collections', 'cId')->integer()->notNull()->autoIncrement()->execute();

        if ($schema->hasColumn('#__storefront_product_collections', 'cllId')) {
            // renameColumn handles AUTO_INCREMENT appropriately for each database
            $schema->renameColumn('#__storefront_product_collections', 'cllId', 'pcId')
                ->integer()
                ->notNull()
                ->autoIncrement()
                ->execute();
        }
    }

    public function down()
    {
        $schema = $this->db->schema();

        $schema->dropColumn('#__storefront_collections', 'cAlias');

        $schema->modifyColumn('#__storefront_collections', 'cId')->char(50)->nullable()->default(null);

        if ($schema->hasColumn('#__storefront_product_collections', 'pcId')) {
            $schema->renameColumn('#__storefront_product_collections', 'pcId', 'cllId')->integer();
        }
    }
}
