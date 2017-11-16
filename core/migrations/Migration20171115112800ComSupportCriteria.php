<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script to create the jos_support_criteria table
 **/
class Migration20171115112800ComSupportCriteria extends Base
{
	static $tableName = '#__support_criteria';

	public function up()
	{
		$tableName = self::$tableName;

		if (!$this->db->tableExists($tableName))
		{
			$createTable = "CREATE TABLE `{$tableName}` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`description` varchar(255) DEFAULT NULL,
				`query` varchar(255) DEFAULT NULL,
				`created` timestamp NULL DEFAULT NULL,
				`modified` timestamp NULL DEFAULT NULL,
				PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1;";

			$this->db->setQuery($createTable);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$tableName = self::$tableName;

		if ($this->db->tableExists($tableName))
		{
			$dropTable = "DROP TABLE {$tableName};";
			$this->db->setQuery($dropTable);
			$this->db->query();
		}
	}
}
