<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for logging media tracking detailed usage
 **/
class Migration20130826150814ComResources extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query = "CREATE TABLE IF NOT EXISTS `#__media_tracking_detailed` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`user_id` int(11) DEFAULT NULL,
				`session_id` varchar(200) DEFAULT NULL,
				`ip_address` varchar(100) DEFAULT NULL,
				`object_id` int(11) DEFAULT NULL,
				`object_type` varchar(100) DEFAULT NULL,
				`object_duration` int(11) DEFAULT NULL,
				`current_position` int(11) DEFAULT NULL,
				`farthest_position` int(11) DEFAULT NULL,
				`current_position_timestamp` datetime DEFAULT NULL,
				`farthest_position_timestamp` datetime DEFAULT NULL,
				`completed` int(11) DEFAULT NULL,
				PRIMARY KEY (`id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$query = "DROP TABLE `#__media_tracking_detailed`";

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
