<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for creating table #__users_log_auth
 **/
class Migration20150721135541ComUsers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__users_log_auth'))
		{
			$query = "CREATE TABLE `#__users_log_auth` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `user_id` int(11) NOT NULL,
				  `username` varchar(150) DEFAULT NULL,
				  `status` enum('success','failure') DEFAULT NULL,
				  `ip` varchar(15) DEFAULT NULL,
				  `logged` datetime DEFAULT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__users_log_auth'))
		{
			$query = "DROP TABLE `#__users_log_auth`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}