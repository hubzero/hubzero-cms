<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding max_uses column to middleware host table
  *
**/
class Migration20140808195514ComTools extends Base
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

        if ($schema->tableExists('host') && !$schema->hasColumn('host', 'max_uses')) {
            $schema->addColumn('host', 'max_uses')->integer()->notNull()->default(0)->execute();
        }

        if ($schema->tableExists('host') && $schema->hasColumn('host', 'uses')) {
            $schema->modifyColumn('host', 'uses')->integer()->notNull()->default(0)->execute();
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

        $schema = $mwdb->schema();

        if ($schema->tableExists('host') && $schema->hasColumn('host', 'max_uses')) {
            $schema->dropColumn('host', 'max_uses');
        }

        if ($schema->tableExists('host') && $schema->hasColumn('host', 'uses')) {
            $schema->modifyColumn('host', 'uses')->smallInteger()->notNull()->default(0)->execute();
        }
    }
}
