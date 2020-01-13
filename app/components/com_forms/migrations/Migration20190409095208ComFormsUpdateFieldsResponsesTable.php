<?php

use Hubzero\Content\Migration\Base;

// no direct access
defined('_HZEXEC_') or die();

class Migration20190409095208ComFormsUpdateFieldsResponsesTable extends Base
{

	static $tableName = '#__forms_fields_responses';
	static $columnName = 'user_id';

	public function up()
	{
		$columnName = self::$columnName;
		$tableName = self::$tableName;
		$needsColumn = !$this->db->tableHasField($tableName, $columnName);

		$alterTable = "ALTER TABLE $tableName ADD COLUMN `$columnName` int(12) unsigned NOT NULL;";

		if ($this->db->tableExists($tableName) && $needsColumn)
		{
			$this->db->setQuery($alterTable);
			$this->db->query();
		}
	}

	public function down()
	{
		$columnName = self::$columnName;
		$tableName = self::$tableName;
		$hasColumn = $this->db->tableHasField($tableName, $columnName);

		$alterTable = "ALTER TABLE $tableName DROP COLUMN $columnName;";

		if ($this->db->tableExists($tableName) && $hasColumn)
		{
			$this->db->setQuery($alterTable);
			$this->db->query();
		}
	}

}
