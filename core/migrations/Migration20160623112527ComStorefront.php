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
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__storefront_collections')
            && $schema->hasColumn('#__storefront_collections', 'cId')
        ) {
            // modifyColumn handles AUTO_INCREMENT appropriately for each database
            $schema->modifyColumn('#__storefront_collections', 'cId')
                ->integer(16)
                ->unsigned()
                ->notNull()
                ->autoIncrement()
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__storefront_collections')
            && $schema->hasColumn('#__storefront_collections', 'cId')
        ) {
            // Note: char(50) with AUTO_INCREMENT is unusual; keeping for backward compatibility
            $schema->modifyColumn('#__storefront_collections', 'cId')
                ->char(50)
                ->notNull()
                ->execute();
        }
    }
}
