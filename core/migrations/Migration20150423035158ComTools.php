<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding zones parameters
 *
*/
class Migration20150423035158ComTools extends Base
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
            && !$mwSchema->hasColumn('zones', 'params')
            && $mwSchema->hasColumn('zones', 'description')
        ) {
            $mwSchema->addColumn('zones', 'params')->text()->nullable();
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

        if ($mwSchema->tableExists('zones') && $mwSchema->hasColumn('zones', 'params')) {
            $mwSchema->dropColumn('zones', 'params');
        }
    }
}
