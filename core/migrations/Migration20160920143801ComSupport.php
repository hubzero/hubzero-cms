<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script to drop deprecated support resolutions table
 **/
class Migration20160920143801ComSupport extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__support_resolutions'))
		{
			$query = "DROP TABLE IF EXISTS `#__support_resolutions`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if (!$this->db->tableExists('#__support_resolutions'))
		{
			$query = "CREATE TABLE `#__support_resolutions` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `title` varchar(100) NOT NULL DEFAULT '',
			  `alias` varchar(100) NOT NULL DEFAULT '',
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
