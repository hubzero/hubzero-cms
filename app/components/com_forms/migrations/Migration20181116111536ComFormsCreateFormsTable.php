<?php

use Hubzero\Content\Migration\Base;

// no direct access
defined('_HZEXEC_') or die();

class Migration20181116111536ComFormsCreateFormsTable extends Base
{

	static $tableName = '#__forms_forms';

	public function up()
	{
		$tableName = self::$tableName;

		$createTable = "CREATE TABLE $tableName (
			`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`name` varchar(350) NOT NULL,
			`description` text NULL DEFAULT NULL,
			`opening_time` timestamp NULL DEFAULT NULL,
			`closing_time` timestamp NULL DEFAULT NULL,
			`disabled` tinyint(1) NULL DEFAULT 0,
			`archived` tinyint(1) NULL DEFAULT 0,
			`responses_locked` tinyint(1) NULL DEFAULT 1,
			`created` timestamp NULL DEFAULT NULL,
			`created_by` int(11) unsigned NOT NULL,
			`modified` timestamp NULL DEFAULT NULL,
			`modified_by` int(11) unsigned NULL DEFAULT NULL,
			PRIMARY KEY (`id`)
		) ENGINE=MYISAM DEFAULT CHARSET=utf8;";

		if (!$this->db->tableExists($tableName))
		{
			$this->db->setQuery($createTable);
			$this->db->query();
		}
	}

	public function down()
	{
		$tableName = self::$tableName;

		$dropTable = "DROP TABLE $tableName";

		if ($this->db->tableExists($tableName))
		{
			$this->db->setQuery($dropTable);
			$this->db->query();
		}
	}

}
