<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding user/tool-session preferences table
 **/
class Migration20150317211600ComTools extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__users_tool_preferences'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__users_tool_preferences` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
				  `params` text,
				  PRIMARY KEY (`id`),
				  UNIQUE KEY `idx_user_id` (`user_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__users_tool_preferences'))
		{
			$query = "DROP TABLE IF EXISTS `#__users_tool_preferences`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}