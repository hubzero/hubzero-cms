<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding zone_id to sessionlog and joblog tables
  *
**/
class Migration20140617153609ComTools extends Base
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

        if (!$mwSchema->hasColumn('sessionlog', 'zone_id')) {
            $mwSchema->addColumn('sessionlog', 'zone_id')->integer()->notNull()->default(0)->execute();
        }
        if (!$mwSchema->hasColumn('joblog', 'zone_id')) {
            $mwSchema->addColumn('joblog', 'zone_id')->integer()->notNull()->default(0)->execute();
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

        if ($mwSchema->hasColumn('sessionlog', 'zone_id')) {
            $mwSchema->dropColumn('sessionlog', 'zone_id');
        }
        if ($mwSchema->hasColumn('joblog', 'zone_id')) {
            $mwSchema->dropColumn('joblog', 'zone_id');
        }
    }
}
