<?php

use Hubzero\Content\Migration\Base;

// no direct access
defined('_HZEXEC_') or die();

class Migration20190117165449ComFormsCreateFormResponsesTable extends Base
{

	static $tableName = '#__forms_form_responses';

	public function up()
	{
		$tableName = self::$tableName;

		$createTable = "CREATE TABLE $tableName (
			`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`form_id` int(11) unsigned NOT NULL,
			`user_id` int(11) unsigned NOT NULL,
			`created` timestamp NULL DEFAULT NULL,
			`modified` timestamp NULL DEFAULT NULL,
			`submitted` timestamp NULL DEFAULT NULL,
			`accepted` timestamp NULL DEFAULT NULL,
			`reviewed_by` int(11) unsigned NULL DEFAULT NULL,
			UNIQUE KEY `form_user` (`form_id`, `user_id`),
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
