<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script to alter jos_support_criteria
 **/
class Migration20171215082900ComSupportCriteriaQueryText extends Base
{
	static $tableName = '#__support_criteria';

	/**
	 * Changes jos_support_criteria query to text
	 **/
	public function up()
	{
		$tableName = self::$tableName;

		if ($this->db->tableExists($tableName))
		{
			$alterTable = "alter table {$tableName} modify query text;";
			$this->db->setQuery($alterTable);
			$this->db->query();
		}
	}

	/**
	 * Changes jos_support_criteria query to varchar(255)
	 **/
	public function down()
	{
		$tableName = self::$tableName;

		if ($this->db->tableExists($tableName))
		{
			$alterTable = "alter table {$tableName} modify query varchar(255);";
			$this->db->setQuery($alterTable);
			$this->db->query();
		}
	}
}
