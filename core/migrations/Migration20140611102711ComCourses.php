<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding a table to track certificate info
 **/
class Migration20140611102711ComCourses extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__courses_certificates'))
		{
			$query = "CREATE TABLE `#__courses_certificates` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `properties` text,
				  `course_id` int(11) NOT NULL DEFAULT '0',
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
		if ($this->db->tableExists('#__courses_certificates'))
		{
			$query = "DROP TABLE `#__courses_certificates`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}