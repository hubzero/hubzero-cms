<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for dropping redundant sessionlog index
 *
*/
class Migration20140804153815Core extends Base
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

        /* We can just drop the old tables because they were never used on a live hub */

        if ($mwdb->schema()->tableExists('sessionlog')) {
            $mwdb->schema()->dropIndex('sessionlog', 'sessnum');
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

        /* We can just drop the old tables because they were never used on a live hub */

        if ($mwdb->schema()->tableExists('sessionlog')) {
            $mwdb->schema()->addUniqueIndex('sessionlog', 'sessnum', 'sessnum');
        }
    }
}
