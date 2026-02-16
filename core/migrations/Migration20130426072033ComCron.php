<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding com cron component
 *
*/
class Migration20130426072033ComCron extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__cron_jobs')) {
            $schema->createTable('#__cron_jobs')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->string('title', 255)->default('')
                ->tinyInteger('state')->default(0)
                ->string('plugin', 255)->default('')
                ->string('event', 255)->default('')
                ->datetime('last_run')->default('0000-00-00 00:00:00')
                ->datetime('next_run')->default('0000-00-00 00:00:00')
                ->string('recurrence', 50)->default('')
                ->datetime('created')->default('0000-00-00 00:00:00')
                ->integer('created_by')->default(0)
                ->datetime('modified')->default('0000-00-00 00:00:00')
                ->integer('modified_by')->default(0)
                ->tinyInteger('active')->default(0)
                ->integer('ordering')->default(0)
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        $this->addComponentEntry('Cron');
        $this->addPluginEntry('cron', 'support');
        $this->addPluginEntry('cron', 'members');
        $this->addPluginEntry('cron', 'cache');
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__cron_jobs')) {
            $schema->dropTable('#__cron_jobs');
        }

        $this->deleteComponentEntry('Cron');
        $this->deletePluginEntry('cron');
    }
}
