<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to add publish_up and publish_down fields to Products and SKUs tables
  *
**/
class Migration20160512000001ComStorefront extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__storefront_products')
            && !$schema->hasColumn('#__storefront_products', 'publish_up')
        ) {
            $schema->addColumn('#__storefront_products', 'publish_up')->datetime()->execute();
            $schema->addColumn('#__storefront_products', 'publish_down')->datetime()->execute();
        }
        if (
            $schema->tableExists('#__storefront_skus')
            && !$schema->hasColumn('#__storefront_skus', 'publish_up')
        ) {
            $schema->addColumn('#__storefront_skus', 'publish_up')->datetime()->execute();
            $schema->addColumn('#__storefront_skus', 'publish_down')->datetime()->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__storefront_products')
            && $schema->hasColumn('#__storefront_products', 'publish_up')
        ) {
            $schema->dropColumn('#__storefront_products', 'publish_up');
            $schema->dropColumn('#__storefront_products', 'publish_down');
        }
        if (
            $schema->tableExists('#__storefront_skus')
            && $schema->hasColumn('#__storefront_skus', 'publish_up')
        ) {
            $schema->dropColumn('#__storefront_skus', 'publish_up');
            $schema->dropColumn('#__storefront_skus', 'publish_down');
        }
    }
}
