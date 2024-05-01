<?php

use Hubzero\Content\Migration\Base;

// no direct access
defined('_HZEXEC_') or die();

class Migration20240206000000PopulateStartingEmailSubscriptions extends Base
{

	static $tableName = '#__email_subscriptions';

	// truncate existing table, insert two records according to its current schema
	public function up()
	{
		$tableName = self::$tableName;
		$now = Date::toSql();
		$auto = $this->db->getAutoIncrement($tableName);

		if ($auto == 0)
		{
			// if legacy table schema from com_reply
			$insertRecords = "INSERT INTO $tableName
				(`description`, `order`, `view`, `required`, `created`)
				VALUES
				('personalizedcommunication', 1, '_personalized_communications_by_interest', 0, '$now'),
				('updates_news', 2, '_updates_and_news', 0, '$now');";

			if ($this->db->tableHasField($tableName, 'profile_field_name')) 
			// if new table schema
			{
				$this->log('Column `profile_field_name` found in table...');

				$insertRecords = "INSERT INTO $tableName
					(`profile_field_name`, `order`, `view`, `created`)
					VALUES
					('personalizedcommunication', 1, '_personalized_communications_by_interest', '$now'),
					('updates_news', 2, '_updates_and_news', '$now');";
			}

			$this->db->setQuery($insertRecords);
			$this->db->query();
		}
	}

	public function down()
	{
		// manually delete records
	}

}
