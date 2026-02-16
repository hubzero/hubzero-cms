<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for renaming venue_id to zone_id on host table
  *
**/
class Migration20140505141538ComTools extends Base
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
            $mwdb->schema()->tableExists('host')
            && $mwSchema->hasColumn('host', 'venue_id')
            && !$mwSchema->hasColumn('host', 'zone_id')
        ) {
            $mwSchema->renameColumn('host', 'venue_id', 'zone_id')
                ->integer()
                ->nullable()
                ->execute();
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

        if (
            $mwdb->schema()->tableExists('host')
            && !$mwSchema->hasColumn('host', 'venue_id')
            && $mwSchema->hasColumn('host', 'zone_id')
        ) {
            $mwSchema->renameColumn('host', 'zone_id', 'venue_id')
                ->integer()
                ->nullable()
                ->execute();
        }
    }
}
