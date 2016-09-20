<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script to drop deprecated support sections table
 **/
class Migration20160920144401ComSupport extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__support_sections'))
		{
			$query = "DROP TABLE IF EXISTS `#__support_sections`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if (!$this->db->tableExists('#__support_sections'))
		{
			$query = "CREATE TABLE `#__support_sections` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `section` varchar(50) DEFAULT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
