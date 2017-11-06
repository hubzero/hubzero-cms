<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script to create the jos_safekids_events_access_methods table
 **/
class Migration20171102100900PlgGroupsForumUsersCategories extends Base
{
	static $tableName = '#__forum_users_categories';

	public function up()
	{
		$tableName = self::$tableName;

		if (!$this->db->tableExists($tableName))
		{
			$createTable = "CREATE TABLE `{$tableName}` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`user_id` int(11) unsigned NOT NULL,
				`category_id` int(11) unsigned	NOT NULL,
				`created` timestamp NULL DEFAULT NULL,
					PRIMARY KEY (`id`)
				)
				ENGINE=MyISAM
				DEFAULT CHARSET=latin1;";
		}

		$this->db->setQuery($createTable);
		$this->db->query();
	}

	public function	down()
	{
		$tableName = self::$tableName;

		if ($this->db->tableExists($tableName))
		{
			$dropTable = "DROP TABLE {$tableName};";
			$this->db->setQuery($dropTable);
			$this->db->query();
		}

	}

}
