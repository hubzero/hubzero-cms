<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding resource media tracking table
 **/
class Migration20130220000000ComResources extends Base
{
	public function up()
	{
		if (!$this->db->tableExists('#__media_tracking'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__media_tracking` (
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
				`total_views` int(11) DEFAULT NULL,
				PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	public function down()
	{
		if ($this->db->tableExists('#__media_tracking'))
		{
			$query = "DROP TABLE IF EXISTS `#__media_tracking`";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
