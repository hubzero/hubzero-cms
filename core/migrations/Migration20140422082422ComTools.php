<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to add params to session table
 *
*/
class Migration20140422082422ComTools extends Base
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

        if ($mwSchema->tableExists('session')) {
            if (!$mwSchema->hasColumn('session', 'params')) {
                $mwSchema->addColumn('params')->text()->nullable();
            }
            if (!$mwSchema->hasColumn('session', 'zone_id')) {
                $mwSchema->addColumn('zone_id')->integer()->notNull()->default(0);
            }
        }
    }

    /**
     * Up
     **/
    public function down()
    {
        if (!$mwdb = $this->getMWDBO()) {
            $this->setError('Failed to connect to the middleware database', 'warning');
            return false;
        }

        $mwSchema = $mwdb->schema();

        if ($mwSchema->tableExists('session')) {
            if ($mwSchema->hasColumn('session', 'params')) {
                $mwSchema->dropColumn('session', 'params');
            }
            if ($mwSchema->hasColumn('session', 'zone_id')) {
                $mwSchema->dropColumn('session', 'zone_id');
            }
        }
    }
}
