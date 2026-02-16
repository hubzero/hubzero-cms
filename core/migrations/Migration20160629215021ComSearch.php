<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for creating the indexQueue table
**/
class Migration20160629215021ComSearch extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__search_indexqueue')) {
            $schema->createTable('#__search_indexqueue')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->string('hubtype', 12)->default('')
                ->string('action', 12)->nullable()
                ->integer('start')->default(0)
                ->tinyInteger('lock')->default(0)
                ->tinyInteger('complete')->default(0)
                ->timestamp('created')->nullable()
                ->integer('created_by')->nullable()
                ->timestamp('modified')->nullable()
                ->primaryKey('id')
                ->engine('InnoDB')
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

        $schema->dropTable('#__search_indexqueue');
    }
}
