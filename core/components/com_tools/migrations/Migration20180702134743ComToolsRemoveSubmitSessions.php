<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// no direct access
defined('_HZEXEC_') or die();

/*
 * Migration for creating the _tool_submit_sessions table
 */
class Migration20180702134743ComToolsRemoveSubmitSessions extends Base
{

	static $submitTable = '#__tool_submit_sessions';
	static $migrationsTable = '#__migrations';
	static $previousMigrationFile = 'Migration20180618142613ComToolsSubmitSessions.php';

	public function up()
	{
		// drop submit sessions table
		$submitTable = self::$submitTable;

		$dropTable = "DROP TABLE $submitTable;";

		if ($this->db->tableExists($submitTable))
		{
			$this->db->setQuery($dropTable);
			$this->db->query();
		}

		// remove record of previous migrations
		$migrationsTable = self::$migrationsTable;
		$previousMigrationFile = self::$previousMigrationFile;

		$deleteMigrationRecords = "DELETE from $migrationsTable"
			. " where file = '$previousMigrationFile';";

		if ($this->db->tableExists($migrationsTable))
		{
			$this->db->setQuery($deleteMigrationRecords);
			$this->db->query();
		}
	}

	public function down()
	{
		// stub
	}

}
