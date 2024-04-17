<?php

use Hubzero\Content\Migration\Base;

// no direct access
defined('_HZEXEC_') or die();

class Migration20240207000000AlterEmailSubscriptionsTable extends Base
{

	static $tableName = '#__email_subscriptions';

	public function up()
	{
		$tableName = self::$tableName;
		$now = Date::toSql();

		$renameDescription = "ALTER TABLE $tableName
			CHANGE COLUMN description profile_field_name varchar(255);";

		$dropRequired = "ALTER TABLE $tableName DROP COLUMN required;";

		if ($this->db->tableExists($tableName))
		{
			$this->db->setQuery($renameDescription);
			$this->db->query();

			$this->db->setQuery($dropRequired);
			$this->db->query();

		}
	}

	public function down()
	{
		$tableName = self::$tableName;
		$now = Date::toSql();

		$renameDescription = "ALTER TABLE $tableName
			CHANGE COLUMN profile_field_name description varchar(255);";

		$addRequired = "ALTER TABLE $tableName ADD required tinyint(1);";

		if ($this->db->tableExists($tableName))
		{
			$this->db->setQuery($renameDescription);
			$this->db->query();

			$this->db->setQuery($dropRequired);
			$this->db->query();
		}
	}

}
