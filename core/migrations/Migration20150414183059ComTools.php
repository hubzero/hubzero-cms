<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding default zone field
 *
*/
class Migration20150414183059ComTools extends Base
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
            $mwSchema->tableExists('zones')
            && $mwSchema->hasColumn('zones', 'state')
            && !$mwSchema->hasColumn('zones', 'is_default')
        ) {
            $mwSchema->addColumn('zones', 'is_default')->tinyInteger(2)->notNull()->default(0);

            // Set the first zone as default
            $mwdb->getQuery(true)
                ->update('zones')
                ->set(['is_default' => 1])
                ->where('type', '=', 'local')
                ->order('id', 'ASC')
                ->limit(1)
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
            $mwSchema->tableExists('zones')
            && $mwSchema->hasColumn('zones', 'is_default')
        ) {
            $mwSchema->dropColumn('zones', 'is_default');
        }
    }
}
