<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding new token field to job table
**/
class Migration20140925213032ComTools extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        if (!$mwdb = $this->getMWDBO()) {
            $this->setError('Failed to connect to the middleware database', 'warning');
            return false;
        }

        $mwSchema = $mwdb->schema();

        if (
            $mwSchema->tableExists('job')
            && $mwSchema->hasColumn('job', 'active')
            && !$mwSchema->hasColumn('job', 'jobtoken')
        ) {
            $mwSchema->addColumn('job', 'jobtoken')
                ->string(32)
                ->nullable()
                ->default(null)
                ->execute();
        }

        if (
            $mwSchema->tableExists('job')
            && $mwSchema->hasColumn('job', 'jobtoken')
            && $mwSchema->hasColumn('job', 'username')
        ) {
            $mwSchema->addIndex('job', 'idx_username_jobtoken', ['username', 'jobtoken']);
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        if (!$mwdb = $this->getMWDBO()) {
            $this->setError('Failed to connect to the middleware database', 'warning');
            return false;
        }

        $mwSchema = $mwdb->schema();

        if ($mwSchema->tableExists('job')) {
            $mwSchema->dropIndex('job', 'idx_username_jobtoken');
        }

        if ($mwSchema->tableExists('job') && $mwSchema->hasColumn('job', 'jobtoken')) {
            $mwSchema->dropColumn('job', 'jobtoken');
        }
    }
}
