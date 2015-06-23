<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for users reputation table
 **/
class Migration20150623144037ComUsers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__user_reputation'))
		{
			$query = "CREATE TABLE `#__user_reputation` (
						`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
						`user_id` int(11) DEFAULT NULL,
						`spam_count` int(11) NOT NULL DEFAULT '0',
						PRIMARY KEY (`id`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__user_reputation'))
		{
			$query = "DROP TABLE `#__user_reputation`";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}