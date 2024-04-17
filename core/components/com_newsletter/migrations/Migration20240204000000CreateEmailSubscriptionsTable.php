<?php

use Hubzero\Content\Migration\Base;

// no direct access
defined('_HZEXEC_') or die();

class Migration20240204000000CreateEmailSubscriptionsTable extends Base
{

	static $tableName = '#__email_subscriptions';

	public function up()
	{
		$tableName = self::$tableName;

		$createTable = "CREATE TABLE $tableName (
			`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`description` varchar(255) NOT NULL,
			`order` int(11) NOT NULL,
			`view` varchar(255) NULL DEFAULT NULL,
			`required` tinyint(1) NOT NULL DEFAULT 0,
			`created` timestamp NULL DEFAULT NULL,
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
