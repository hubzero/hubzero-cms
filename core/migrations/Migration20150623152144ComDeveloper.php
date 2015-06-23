<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding rate limiting table
 **/
class Migration20150623152144ComDeveloper extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__developer_rate_limit'))
		{
			$query = "CREATE TABLE `#__developer_rate_limit` (
						`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
						`application_id` int(11) DEFAULT NULL,
						`uidNumber` int(11) DEFAULT NULL,
						`ip` varchar(255) DEFAULT NULL,
						`limit_short` int(11) DEFAULT NULL,
						`limit_long` int(11) DEFAULT NULL,
						`count_short` int(11) DEFAULT NULL,
						`count_long` int(11) DEFAULT NULL,
						`expires_short` datetime DEFAULT NULL,
						`expires_long` datetime DEFAULT NULL,
						`created` datetime DEFAULT NULL,
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
		if ($this->db->tableExists('#__developer_rate_limit'))
		{
			$query = "DROP TABLE `#__developer_rate_limit`";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}