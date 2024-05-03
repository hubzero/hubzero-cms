<?php

use Hubzero\Content\Migration\Base;

// no direct access
defined('_HZEXEC_') or die();

class Migration20240212000000PopulatePagesTable extends Base
{

	static $tableName = '#__reply_pages';

	public function up()
	{
		$tableName = self::$tableName;
		$now = Date::toSql();
		$auto = $this->db->getAutoIncrement($tableName);

		// create records only if they do not already exist:
		if ($auto == 0)
		{
			$insertRecords = "INSERT INTO $tableName
				(`description`, `created`)
				VALUES
				('email subscriptions', '$now'),
				('how do you use the Hub?', '$now');";

			if ($this->db->tableExists($tableName))
			{
				$this->db->setQuery($insertRecords);
				$this->db->query();
			}
		}
	}

	public function down()
	{
		// manually delete records
	}

}
