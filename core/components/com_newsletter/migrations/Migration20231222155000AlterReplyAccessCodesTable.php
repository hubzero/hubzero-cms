<?php

use Hubzero\Content\Migration\Base;

// no direct access
defined('_HZEXEC_') or die();

class Migration20231222155000AlterReplyAccessCodesTable extends Base
{

	static $tableName = '#__reply_access_codes';

	public function up()
	{
		$tableName = self::$tableName;
		$now = Date::toSql();

		$dropCode = "ALTER TABLE $tableName DROP COLUMN code;";
		$dropExpire = "ALTER TABLE $tableName DROP COLUMN expiration;";

		if ($this->db->tableExists($tableName))
		{
			$this->db->setQuery($dropCode);
			$this->db->query();

			$this->db->setQuery($dropExpire);
			$this->db->query();

		}
	}

	public function down()
	{
		$tableName = self::$tableName;
		$now = Date::toSql();

		$addCode = "ALTER TABLE $tableName ADD code char(64);";
		$addExpire = "ALTER TABLE $tableName ADD expiration timestamp;";

		if ($this->db->tableExists($tableName))
		{
			$this->db->setQuery($addCode);
			$this->db->query();

			$this->db->setQuery($addExpire);
			$this->db->query();
		}
	}

}
