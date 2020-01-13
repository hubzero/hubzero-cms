<?php

use Hubzero\Content\Migration\Base;

// no direct access
defined('_HZEXEC_') or die();

class Migration20181218155318ComFormsCreatePageFieldsTable extends Base
{

	static $tableName = '#__forms_page_fields';

	public function up()
	{
		$tableName = self::$tableName;

		$createTable = "CREATE TABLE $tableName (
			`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`page_id` int(11) unsigned NOT NULL,
			`order` int(11) unsigned NOT NULL,
			`type` varchar(50) NOT NULL,
			`required` tinyint(1) NULL DEFAULT NULL,
			`label` varchar(255) NULL DEFAULT NULL,
			`name` varchar(255) NULL DEFAULT NULL,
			`help_text` varchar(750) NULL DEFAULT NULL,
			`default_value` varchar(750) NULL DEFAULT NULL,
			`rows` int(11) NULL DEFAULT NULL,
			`max_length` int(11) NULL DEFAULT NULL,
			`inline` tinyint(1) NULL DEFAULT NULL,
			`other` tinyint(1) NULL DEFAULT NULL,
			`values` text NULL DEFAULT NULL,
			`multiple` tinyint(1) NULL DEFAULT NULL,
			`toggle` tinyint(1) NULL DEFAULT NULL,
			`min` int(11) NULL DEFAULT NULL,
			`max` int(11) NULL DEFAULT NULL,
			`step` int(11) NULL DEFAULT NULL,
			`created` timestamp NOT NULL,
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

