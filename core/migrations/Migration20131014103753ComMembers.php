<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding members quota interface
 *
 */
class Migration20131014103753ComMembers extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__users_quotas')) {
            $schema->createTable('#__users_quotas')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('user_id')
                ->integer('class_id')->nullable()
                ->integer('hard_files')
                ->integer('soft_files')
                ->integer('hard_blocks')
                ->integer('soft_blocks')
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__users_quotas_classes')) {
            $schema->createTable('#__users_quotas_classes')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->string('alias', 255)->default('')
                ->integer('hard_files')
                ->integer('soft_files')
                ->integer('hard_blocks')
                ->integer('soft_blocks')
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();

            $this->db->getQuery(true)
                ->insert('#__users_quotas_classes')
                ->set([
                    'id'          => 1,
                    'alias'       => 'default',
                    'hard_files'  => 0,
                    'soft_files'  => 0,
                    'hard_blocks' => 1000000,
                    'soft_blocks' => 900000
                ])
                ->execute();
        }

        if (!$schema->tableExists('#__users_quotas_log')) {
            $schema->createTable('#__users_quotas_log')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->string('object_type', 255)->default('')
                ->integer('object_id')
                ->string('name', 255)->default('')
                ->string('action', 255)->default('')
                ->integer('actor_id')
                ->integer('soft_blocks')
                ->integer('hard_blocks')
                ->integer('soft_files')
                ->integer('hard_files')
                ->primaryKey('id')
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

        $schema->dropTable('#__users_quotas');
        $schema->dropTable('#__users_quotas_classes');
        $schema->dropTable('#__users_quotas_log');
    }
}
