<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for renaming migrations table
 *
*/
class Migration20140502184454Core extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('migrations') && !$schema->tableExists('#__migrations')) {
            $schema->renameTable('migrations', '#__migrations');

            $this->callback('migration', 'setTableName', array('#__migrations'));
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('migrations') && $schema->tableExists('#__migrations')) {
            $schema->renameTable('#__migrations', 'migrations');

            $this->callback('migration', 'setTableName', array('migrations'));
        }
    }
}
