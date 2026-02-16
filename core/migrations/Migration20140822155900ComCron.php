<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding indexes on #__cron_jobs
 *
*/
class Migration20140822155900ComCron extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__cron_jobs')) {
            $schema->addIndex('#__cron_jobs', 'idx_state', 'state');

            $schema->addIndex('#__cron_jobs', 'idx_created_by', 'created_by');
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__cron_jobs')) {
            $schema->dropIndex('#__cron_jobs', 'idx_state');

            $schema->dropIndex('#__cron_jobs', 'idx_created_by');
        }
    }
}
