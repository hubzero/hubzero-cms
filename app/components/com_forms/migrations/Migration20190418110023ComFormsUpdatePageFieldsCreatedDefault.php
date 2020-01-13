<?php

use Hubzero\Content\Migration\Base;

// no direct access
defined('_HZEXEC_') or die();

class Migration20190418110023ComFormsUpdatePageFieldsCreatedDefault extends Base
{

	static $tableName = '#__forms_page_fields';
	static $columnName = 'created';

	public function up()
	{
		$columnName = self::$columnName;
		$tableName = self::$tableName;

		$alterTable = "ALTER TABLE $tableName CHANGE COLUMN `$columnName` `$columnName` timestamp NULL DEFAULT NULL;";

		if ($this->db->tableExists($tableName))
		{
			$this->db->setQuery($alterTable);
			$this->db->query();
		}
	}

	public function down()
	{
		$columnName = self::$columnName;
		$tableName = self::$tableName;

		$alterTable = "ALTER TABLE $tableName CHANGE COLUMN `$columnName` `$columnName` timestamp NOT NULL;";

		if ($this->db->tableExists($tableName))
		{
			$this->db->setQuery($alterTable);
			$this->db->query();
		}
	}

}
