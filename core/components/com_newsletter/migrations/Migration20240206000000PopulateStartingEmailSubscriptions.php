<?php

use Hubzero\Content\Migration\Base;

// no direct access
defined('_HZEXEC_') or die();

class Migration20240206000000PopulateStartingEmailSubscriptions extends Base
{

	static $tableName = '#__email_subscriptions';

	public function up()
	{
		$tableName = self::$tableName;
		$now = Date::toSql();

		$insertRecords = "INSERT INTO $tableName
			(`description`, `order`, `view`, `required`, `created`)
			VALUES
			('personalizedcommunication', 1, '_personalized_communications_by_interest', 0, '$now'),
			('updates_news', 2, '_updates_and_news', 0, '$now');";

		if ($this->db->tableExists($tableName))
		{
			$this->db->setQuery($insertRecords);
			$this->db->query();
		}
	}

	public function down()
	{
		// manually delete records
	}

}
