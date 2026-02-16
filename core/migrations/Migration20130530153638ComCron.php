<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding twitter authentication plugin
 *
*/
class Migration20130530153638ComCron extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__cron_jobs')
            && !$schema->hasColumn('#__cron_jobs', 'params')
        ) {
            $schema->addColumn('#__cron_jobs', 'params')->text()->notNull();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__cron_jobs', 'params')) {
            $schema->dropColumn('#__cron_jobs', 'params');
        }
    }
}
