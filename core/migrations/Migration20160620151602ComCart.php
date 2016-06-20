<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script to add #__cart_downloads table
 **/
class Migration20160620151602ComCart extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__cart_downloads'))
		{
			$query = "CREATE TABLE `#__cart_downloads` (
			  `dId` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `uId` int(11) DEFAULT NULL,
			  `sId` int(11) DEFAULT NULL,
			  `dDownloaded` datetime DEFAULT NULL,
			  `dStatus` tinyint(1) DEFAULT '1',
			  `dIp` int(10) unsigned DEFAULT NULL,
			  PRIMARY KEY (`dId`),
			  KEY `idx_uId` (`uId`),
			  KEY `idx_sId` (`sId`)
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
		if ($this->db->tableExists('#__cart_downloads'))
		{
			$query = "DROP TABLE `#__cart_downloads`";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
