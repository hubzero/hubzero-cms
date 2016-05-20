<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_HZEXEC_') or die();

/**
 * Migration script to add SKU restrictions by users
 **/
class Migration20160524000001ComStorefront extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__storefront_serials'))
		{
			$query = "	CREATE TABLE `#__storefront_serials` (
						`srId` int(11) unsigned NOT NULL AUTO_INCREMENT,
						`srNumber` varchar(32) DEFAULT NULL,
						`srSId` int(11) DEFAULT NULL,
						`srStatus` varchar(10) DEFAULT NULL,
						PRIMARY KEY (`srId`),
  						UNIQUE KEY `unique keys for a SKU` (`srNumber`,`srSId`)
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
		if ($this->db->tableExists('#__storefront_serials'))
		{
			$query = "DROP TABLE `#__storefront_serials`";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}

}