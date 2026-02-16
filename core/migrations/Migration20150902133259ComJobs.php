<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding expiredate column to jobs table
 *
*/
class Migration20150902133259ComJobs extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__jobs_openings') && !$schema->hasColumn('#__jobs_openings', 'expiredate')) {
            $schema->addColumn('#__jobs_openings', 'expiredate')
                ->datetime()
                ->nullable()
                ->default('0000-00-00 00:00:00')
                ->after('closedate')
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__jobs_openings') && $schema->hasColumn('#__jobs_openings', 'expiredate')) {
            $schema->dropColumn('#__jobs_openings', 'expiredate');
        }
    }
}
