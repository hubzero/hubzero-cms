<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for setting up/updating time component
**/
class Migration20140714201025ComTime extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema->dropTable('#__time_auth_tokens');

        if (!$schema->tableExists('#__time_hub_contacts')) {
            $schema->createTable('#__time_hub_contacts')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->string('name', 255)->default('')
                ->string('phone', 255)->default('000-000-0000')
                ->string('email', 255)->default('')
                ->string('role', 255)->default('')
                ->integer('hub_id')
                ->primaryKey('id')
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
                ->date('date')
                ->longText('description')->nullable()
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        } elseif ($schema->tableExists('#__time_records')) {
            $schema = $this->db->schema();

            if ($schema->hasColumn('#__time_records', 'billed')) {
                $schema->dropColumn('#__time_records', 'billed');
            }
        }

        $schema->dropTable('#__time_reports');
        $schema->dropTable('#__time_reports_records_assoc');

        if (!$schema->tableExists('#__time_tasks')) {
            $schema->createTable('#__time_tasks')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->string('name', 255)->default('')
                ->integer('hub_id')
                ->date('start_date')->default('0000-00-00')
                ->date('end_date')->default('0000-00-00')
                ->integer('active')->default(1)
                ->blob('description')->nullable()
                ->integer('priority')->nullable()
                ->integer('assignee')->nullable()
                ->integer('liaison')->nullable()
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__time_users')) {
            $schema->createTable('#__time_users')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('user_id')
                ->integer('manager_id')
                ->integer('liaison')->default(0)
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        $this->addComponentEntry('Time', 'com_time', 0);
        $this->deletePluginEntry('time');
    }
}
