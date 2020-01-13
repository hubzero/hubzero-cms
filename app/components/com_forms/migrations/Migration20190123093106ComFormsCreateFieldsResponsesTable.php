<?php

use Hubzero\Content\Migration\Base;

// no direct access
defined('_HZEXEC_') or die();

class Migration20190123093106ComFormsCreateFieldsResponsesTable extends Base
{

	static $tableName = '#__forms_fields_responses';

	public function up()
	{
		$tableName = self::$tableName;

		$createTable = "CREATE TABLE $tableName (
			`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`field_id` int(11) unsigned NOT NULL,
			`form_response_id` int(11) unsigned NOT NULL,
			`response` text NULL DEFAULT NULL,
			`created` timestamp NULL DEFAULT NULL,
			UNIQUE KEY `users_field_response` (`field_id`, `form_response_id`),
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
