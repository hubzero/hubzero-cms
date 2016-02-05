<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for creating table #__courses_prerequisites
 **/
class Migration20140606165053ComCourses extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__courses_prerequisites'))
		{
			$query = "CREATE TABLE `#__courses_prerequisites` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `section_id` int(11) NOT NULL DEFAULT '0',
				  `item_scope` varchar(255) NOT NULL DEFAULT 'asset',
				  `item_id` int(11) NOT NULL DEFAULT '0',
				  `requisite_scope` varchar(255) NOT NULL DEFAULT 'asset',
				  `requisite_id` int(11) NOT NULL DEFAULT '0',
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
		if ($this->db->tableExists('#__courses_prerequisites'))
		{
			$query = "DROP TABLE `#__courses_prerequisites`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}