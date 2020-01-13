<?php

use Hubzero\Content\Migration\Base;

// no direct access
defined('_HZEXEC_') or die();

class Migration20190417111849ComFormsAddModifiedToFieldsResponsesTable extends Base
{

	static $tableName = '#__forms_fields_responses';
	static $modifiedColumn = 'modified';
	static $modifiedByColumn = 'modified_by';

	public function up()
	{
		$tableName = self::$tableName;
		$modifiedColumn = self::$modifiedColumn;
		$modifiedByColumn = self::$modifiedByColumn;
		$needsModified = !$this->db->tableHasField($tableName, $modifiedColumn);
		$needsModifiedBy = !$this->db->tableHasField($tableName, $modifiedByColumn);

		$addModified = "ALTER TABLE $tableName ADD COLUMN `$modifiedColumn` timestamp NULL DEFAULT NULL;";
		$addModifiedBy = "ALTER TABLE $tableName ADD COLUMN `$modifiedByColumn` int(11) NULL DEFAULT NULL;";

		if ($this->db->tableExists($tableName) && $needsModified)
		{
			$this->db->setQuery($addModified);
			$this->db->query();
		}

		if ($this->db->tableExists($tableName) && $needsModifiedBy)
		{
			$this->db->setQuery($addModifiedBy);
			$this->db->query();
		}
	}

	public function down()
	{
		$tableName = self::$tableName;
		$modifiedColumn = self::$modifiedColumn;
		$modifiedByColumn = self::$modifiedByColumn;
		$hasModified = $this->db->tableHasField($tableName, $modifiedColumn);
		$hasModifiedBy = $this->db->tableHasField($tableName, $modifiedByColumn);

		$dropModified = "ALTER TABLE $tableName DROP COLUMN $modifiedColumn;";
		$dropModifiedBy = "ALTER TABLE $tableName DROP COLUMN $modifiedByColumn;";

		if ($this->db->tableExists($tableName) && $hasModified)
		{
			$this->db->setQuery($dropModified);
			$this->db->query();
		}

		if ($this->db->tableExists($tableName) && $hasModifiedBy)
		{
			$this->db->setQuery($dropModifiedBy);
			$this->db->query();
		}
	}

}
