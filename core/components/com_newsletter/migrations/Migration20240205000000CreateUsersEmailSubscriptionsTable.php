<?php

use Hubzero\Content\Migration\Base;

// no direct access
defined('_HZEXEC_') or die();

class Migration20240205000000CreateUsersEmailSubscriptionsTable extends Base
{

	static $tableName = '#__users_email_subscriptions';

	public function up()
	{
		$tableName = self::$tableName;

		$createTable = "CREATE TABLE $tableName (
			`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`user_id` int(11) unsigned NOT NULL,
			`email_subscription_id` int(11) unsigned NOT NULL,
			`subscribed` bool NULL DEFAULT NULL,
			`created` timestamp NULL DEFAULT NULL,
			`modified` timestamp NULL DEFAULT NULL,
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
