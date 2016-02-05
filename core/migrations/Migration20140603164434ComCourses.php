<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for creating table #__courses_progress_factors
 **/
class Migration20140603164434ComCourses extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__courses_progress_factors'))
		{
			$query = "CREATE TABLE `#__courses_progress_factors` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `section_id` int(11) NOT NULL,
				  `asset_id` int(11) NOT NULL,
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
		if ($this->db->tableExists('#__courses_progress_factors'))
		{
			$query = "DROP TABLE `#__courses_progress_factors`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}