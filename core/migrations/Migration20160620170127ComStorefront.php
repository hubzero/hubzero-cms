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
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__storefront_products')
            && !$schema->hasColumn('#__storefront_products', 'access')
        ) {
            $schema->addColumn('#__storefront_products', 'access')->tinyInteger(3)->notNull()->default('0');
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
            && $schema->hasColumn('#__storefront_products', 'access')
        ) {
            $schema->dropColumn('#__storefront_products', 'access');
        }
    }
}
