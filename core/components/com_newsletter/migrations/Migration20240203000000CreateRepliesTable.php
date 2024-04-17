<?php

use Hubzero\Content\Migration\Base;

// no direct access
defined('_HZEXEC_') or die();

class Migration20240203000000CreateRepliesTable extends Base
{

	static $tableName = '#__reply_replies';

	public function up()
	{
		$tableName = self::$tableName;

		$createTable = "CREATE TABLE $tableName (
			`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`user_id` int(11) unsigned NOT NULL,
			`page_id` int(11) unsigned NOT NULL,
			`input` text NOT NULL,
			`created` timestamp NOT NULL,
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
