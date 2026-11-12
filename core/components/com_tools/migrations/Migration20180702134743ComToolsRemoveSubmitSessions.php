<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Migrations;

use Hubzero\Content\Migration\Base;

// no direct access
/*
 * Migration for creating the _tool_submit_sessions table
 */
class Migration20180702134743ComToolsRemoveSubmitSessions extends Base
{
    /**
     * @var  string
     */
    protected static $submitTable = '#__tool_submit_sessions';
    /**
     * @var  string
     */
    protected static $migrationsTable = '#__migrations';
    /**
     * @var  string
     */
    protected static $previousMigrationFile = 'Migration20180618142613ComToolsSubmitSessions.php';

    /**
     * Up
     *
     * @return  void
     */
    public function up()
    {
        // drop submit sessions table
        $submitTable = self::$submitTable;

        $dropTable = "DROP TABLE $submitTable;";

        if ($this->db->tableExists($submitTable)) {
            $this->db->setQuery($dropTable);
            $this->db->query();
        }

        // remove record of previous migrations
        $migrationsTable = self::$migrationsTable;
        $previousMigrationFile = self::$previousMigrationFile;

        $deleteMigrationRecords = "DELETE from $migrationsTable"
            . " where file = '$previousMigrationFile';";

        if ($this->db->tableExists($migrationsTable)) {
            $this->db->setQuery($deleteMigrationRecords);
            $this->db->query();
        }
    }

    /**
     * Down
     *
     * @return  void
     */
    public function down()
    {
        // stub
    }
}
