<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding publish_up and publish_down fields to cron table
**/
class Migration20140305090721ComCron extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->hasColumn('#__cron_jobs', 'publish_up')) {
            $schema->addColumn('#__cron_jobs', 'publish_up')
                ->datetime()
                ->notNull()
                ->default('0000-00-00 00:00:00')
                ->execute();
        }

        if (!$schema->hasColumn('#__cron_jobs', 'publish_down')) {
            $schema->addColumn('#__cron_jobs', 'publish_down')
                ->datetime()
                ->notNull()
                ->default('0000-00-00 00:00:00')
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        // drop publish_up
        if ($schema->hasColumn('#__cron_jobs', 'publish_up')) {
            $schema->dropColumn('#__cron_jobs', 'publish_up');
        }

        // drop publish_down
        if ($schema->hasColumn('#__cron_jobs', 'publish_down')) {
            $schema->dropColumn('#__cron_jobs', 'publish_down');
        }
    }
}
