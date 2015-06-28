<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding unity asset table
 **/
class Migration20140212162812ComCourses extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__courses_asset_unity'))
		{
			$query = "CREATE TABLE `#__courses_asset_unity` (
					  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
					  `member_id` int(11) NOT NULL,
					  `asset_id` int(11) NOT NULL,
					  `created` datetime NOT NULL,
					  `passed` tinyint(1) NOT NULL,
					  `details` text,
					  PRIMARY KEY (`id`)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__courses_asset_unity'))
		{
			$query = "DROP TABLE `#__courses_asset_unity`";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}