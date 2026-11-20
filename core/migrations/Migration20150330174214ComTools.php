<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding description field to zones
  *
**/
class Migration20150330174214ComTools extends Base
{
    /**
     * Up
     *
     * @return  false|null
     **/
    public function up()
    {
        if (!$mwdb = $this->getMWDBO()) {
            $this->setError('Failed to connect to the middleware database', 'warning');
            return false;
        }

        $mwSchema = $mwdb->schema();

        if ($mwdb->schema()->tableExists('zones')) {
            if (!$mwSchema->hasColumn('zones', 'description')) {
                $mwSchema->addColumn('zones', 'description')->text();
            }
        }

        return null;
    }

    /**
     * Down
     *
     * @return  false|null
     **/
    public function down()
    {
        if (!$mwdb = $this->getMWDBO()) {
            $this->setError('Failed to connect to the middleware database', 'warning');
            return false;
        }

        $mwSchema = $mwdb->schema();

        if ($mwdb->schema()->tableExists('zones')) {
            if ($mwSchema->hasColumn('zones', 'description')) {
                $mwSchema->dropColumn('zones', 'description');
            }
        }

        return null;
    }
}
