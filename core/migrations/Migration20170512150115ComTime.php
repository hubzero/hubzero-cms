<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for removing com_time and associated extensions and tables
 *
*/
class Migration20170512150115ComTime extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        $this->deleteComponentEntry('time');

        $this->deletePluginEntry('support', 'time');

        $this->deletePluginEntry('time', 'csv');
        $this->deletePluginEntry('time', 'summary');
        $this->deletePluginEntry('time', 'weeklybar');

        $tables = array(
            '#__time_auth_token',
            '#__time_funding_allocations',
            '#__time_funding_sources',
            '#__time_hub_allotments',
            '#__time_hub_contacts',
            '#__time_hubs',
            '#__time_liaisons',
            '#__time_proxies',
            '#__time_records',
            '#__time_tasks'
        );

        foreach ($tables as $table) {
            $schema->dropTable($table);
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        $this->addComponentEntry('time');

        $this->addPluginEntry('support', 'time');

        $this->addPluginEntry('time', 'csv');
        $this->addPluginEntry('time', 'summary');
        $this->addPluginEntry('time', 'weeklybar');

        if (!$schema->tableExists('#__time_auth_token')) {
            $schema->createTable('#__time_auth_token')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('user_id')
                ->string('token', 255)->default('')
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__time_funding_allocations')) {
            $schema->createTable('#__time_funding_allocations')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->unsignedInteger('user_id')->default(0)
                ->unsignedInteger('source_id')->default(0)
                ->integer('percentage')->default(0)
                ->primaryKey('id')
                ->index('idx_user_id', 'user_id')
                ->index('idx_source_id', 'source_id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__time_funding_sources')) {
            $schema->createTable('#__time_funding_sources')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->string('title', 255)->default('')
                ->datetime('created')->default('0000-00-00 00:00:00')
                ->integer('created_by')->default(0)
                ->string('type', 150)->default('')
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__time_hub_allotments')) {
            $schema->createTable('#__time_hub_allotments')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->unsignedInteger('hub_id')->default(0)
                ->date('start_date')->default('0000-00-00')
                ->date('end_date')->default('0000-00-00')
                ->double('hours')->default(0)
                ->primaryKey('id')
                ->index('idx_hub_id', 'hub_id')
                ->engine('InnoDB')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__time_hub_contacts')) {
            $schema->createTable('#__time_hub_contacts')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->string('name', 255)->default('')
                ->string('phone', 255)->default('000-000-0000')
                ->string('email', 255)->default('')
                ->string('role', 255)->default('')
                ->integer('hub_id')
                ->primaryKey('id')
                ->index('idx_hub_id', 'hub_id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__time_hubs')) {
            $schema->createTable('#__time_hubs')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->string('name', 255)->default('')
                ->string('name_normalized', 255)->default('')
                ->string('liaison', 255)->nullable()
                ->date('anniversary_date')->default('0000-00-00')
                ->string('support_level', 255)->default('Standard Support')
                ->integer('active')->default(1)
                ->blob('notes')->nullable()
                ->integer('asset_id')->nullable()
                ->primaryKey('id')
                ->index('idx_active', 'active')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__time_liaisons')) {
            $schema->createTable('#__time_liaisons')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('user_id')->nullable()
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__time_proxies')) {
            $schema->createTable('#__time_proxies')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('user_id')
                ->integer('proxy_id')->nullable()
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__time_records')) {
            $schema->createTable('#__time_records')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('task_id')
                ->integer('user_id')
                ->double('time')
                ->datetime('date')
                ->datetime('end')
                ->longText('description')->nullable()
                ->primaryKey('id')
                ->index('idx_task_id', 'task_id')
                ->index('idx_user_id', 'user_id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__time_tasks')) {
            $schema->createTable('#__time_tasks')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->string('name', 255)->default('')
                ->integer('hub_id')
                ->date('start_date')->default('0000-00-00')
                ->date('end_date')->default('0000-00-00')
                ->integer('active')->default(1)
                ->blob('description')->nullable()
                ->integer('priority')->default(0)
                ->integer('assignee_id')->default(0)
                ->integer('liaison_id')->default(0)
                ->tinyInteger('billable')->default(0)
                ->primaryKey('id')
                ->index('idx_hub_id', 'hub_id')
                ->index('idx_liaison_id', 'liaison_id')
                ->index('idx_assignee_id', 'assignee_id')
                ->index('idx_priority', 'priority')
                ->index('idx_billable', 'billable')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }
    }
}
