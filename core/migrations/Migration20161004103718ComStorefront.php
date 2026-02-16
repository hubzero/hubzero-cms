<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to add `exclude` column to storefront access_groups table
 *
*/
class Migration20161004103718ComStorefront extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__storefront_product_access_groups')
            && !$schema->hasColumn('#__storefront_product_access_groups', 'exclude')
        ) {
            $schema->addColumn('#__storefront_product_access_groups', 'exclude')
                ->tinyInteger(2)
                ->notNull()
                ->default('0');
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__storefront_product_access_groups')
            && $schema->hasColumn('#__storefront_product_access_groups', 'exclude')
        ) {
            $schema->dropColumn('#__storefront_product_access_groups', 'exclude');
        }
    }
}
