<?php

use Hubzero\Content\Migration\Base;

// no direct access
defined('_HZEXEC_') or die();

class Migration20181211160419ComFormsCreateFormPagesTable extends Base
{

	static $tableName = '#__forms_form_pages';

	public function up()
	{
		$tableName = self::$tableName;

		$createTable = "CREATE TABLE $tableName (
			`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`form_id` int(11) unsigned NOT NULL,
			`order` int(11) unsigned NOT NULL,
			`title` varchar(350) NULL DEFAULT NULL,
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

