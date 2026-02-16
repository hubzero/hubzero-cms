<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding item_watch table
  *
**/
class Migration20150518181100ComPublications extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__item_watch')) {
            $schema->createTable('#__item_watch')
                ->integer('id', ['autoIncrement' => true])
                ->integer('item_id')->default(0)
                ->string('item_type', 150)
                ->datetime('created')->default('0000-00-00 00:00:00')
                ->integer('created_by')->default(0)
                ->string('email', 150)->nullable()
                ->tinyInteger('state')->default(0)
                ->text('params')->nullable()
                ->primaryKey('id')
                ->index('idx_item_type_item_id', ['item_type', 'item_id'])
                ->index('idx_created_by_email', ['created_by', 'email'])
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        $schema->dropTable('#__item_watch');
    }
}
