<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for creating table #__users_merge_log
 **/
class Migration20140626155344ComUsers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__users_merge_log'))
		{
			$query = "CREATE TABLE `#__users_merge_log` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `source` varchar(150) NOT NULL DEFAULT '',
				  `destination` varchar(150) NOT NULL DEFAULT '',
				  `table` varchar(255) NOT NULL DEFAULT '',
				  `column` varchar(255) NOT NULL DEFAULT '',
				  `table_pk` varchar(255) DEFAULT NULL,
				  `table_id` int(11) DEFAULT NULL,
				  `logged` datetime NOT NULL,
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
		if ($this->db->tableExists('#__users_merge_log'))
		{
			$query = "DROP TABLE `#__users_merge_log`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}