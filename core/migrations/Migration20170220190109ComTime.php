<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding indexes and 'billable' column to #__time tables
 *
*/
class Migration20170220190109ComTime extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__time_tasks')) {
            $schema->addIndex('#__time_tasks', 'idx_hub_id', 'hub_id');

            if ($schema->hasColumn('#__time_tasks', 'liaison_id')) {
                $schema->modifyColumn('#__time_tasks', 'liaison_id')->integer()->notNull()->default(0)->execute();
                $schema->addIndex('#__time_tasks', 'idx_liaison_id', 'liaison_id');
            }

            if ($schema->hasColumn('#__time_tasks', 'assignee_id')) {
                $schema->modifyColumn('#__time_tasks', 'assignee_id')->integer()->notNull()->default(0)->execute();
                $schema->addIndex('#__time_tasks', 'idx_assignee_id', 'assignee_id');
            }

            if ($schema->hasColumn('#__time_tasks', 'priority')) {
                $schema->modifyColumn('#__time_tasks', 'priority')->integer()->notNull()->default(0)->execute();
                $schema->addIndex('#__time_tasks', 'idx_priority', 'priority');
            }

            if (!$schema->hasColumn('#__time_tasks', 'billable')) {
                $schema->addColumn('#__time_tasks', 'billable')->tinyInteger()->default(0)->execute();
            }

            $schema->addIndex('#__time_tasks', 'idx_billable', 'billable');
        }

        if ($schema->tableExists('#__time_records')) {
            $schema->table('#__time_records')->alter()
                ->addIndex('idx_task_id', 'task_id')
                ->addIndex('idx_user_id', 'user_id')
                ->execute();
        }

        if ($schema->tableExists('#__time_hubs')) {
            $schema->addIndex('#__time_hubs', 'idx_active', 'active');
        }

        if ($schema->tableExists('#__time_hub_contacts')) {
            $schema->addIndex('#__time_hub_contacts', 'idx_hub_id', 'hub_id');
        }

        if (!$schema->tableExists('#__time_hub_allotments')) {
            $schema->table('#__time_hub_allotments')->create()
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
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__time_tasks')) {
            $schema->table('#__time_tasks')->alter()
                ->dropIndex('idx_assignee_id')
                ->dropIndex('idx_liaison_id')
                ->dropIndex('idx_priority')
                ->dropIndex('idx_billable')
                ->execute();

            if ($schema->hasColumn('#__time_tasks', 'billable')) {
                $schema->table('#__time_tasks')->alter()
                    ->dropColumn('billable')
                    ->execute();
            }
        }

        if ($schema->tableExists('#__time_records')) {
            $schema->table('#__time_records')->alter()
                ->dropIndex('idx_task_id')
                ->dropIndex('idx_user_id')
                ->execute();
        }

        if ($schema->tableExists('#__time_hubs')) {
            $schema->dropIndex('#__time_hubs', 'idx_active');
        }

        if ($schema->tableExists('#__time_hub_contacts')) {
            $schema->dropIndex('#__time_hub_contacts', 'idx_hub_id');
        }

        if ($schema->tableExists('#__time_hub_allotments')) {
            $schema->table('#__time_hub_allotments')->drop();
        }
    }
}
