<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for fixing joblob primary key
**/
class Migration20140813200031ComTools extends Base
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

        $schema = $mwdb->schema();

        if ($schema->tableExists('joblog')) {
            if (!$schema->hasPrimaryKeyColumn('joblog', 'venue')) {
                $schema->table('joblog')->alter()
                    ->dropPrimaryKey()
                    ->addPrimaryKey(['sessnum', 'job', 'event', 'venue'])
                    ->execute();
            }
        }
    }
}
