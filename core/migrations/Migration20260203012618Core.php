<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to add execution_time column to migrations table
 *
 * This allows tracking how long each migration takes to run, which is
 * useful for identifying slow migrations and for monitoring purposes.
 */
class Migration20260203012618Core extends Base
{
    /**
     * Up
     */
    public function up()
    {
        // Check for both possible table names
        $tableName = $this->db->tableExists('migrations') ? 'migrations' : '#__migrations';

        if (!$this->db->tableHasField($tableName, 'execution_time')) {
            $this->db->schema()->alterTable($tableName)
                ->addColumn('execution_time')
                ->integer()
                ->unsigned()
                ->nullable()
                ->comment('Duration in milliseconds')
                ->execute();
        }
    }

    /**
     * Down
     */
    public function down()
    {
        // Check for both possible table names
        $tableName = $this->db->tableExists('migrations') ? 'migrations' : '#__migrations';

        if ($this->db->tableHasField($tableName, 'execution_time')) {
            $this->db->schema()->alterTable($tableName)
                ->dropColumn('execution_time')
                ->execute();
        }
    }
}
