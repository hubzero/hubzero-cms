<?php

use Hubzero\Content\Migration\Base;

<<<<<<< HEAD
// No direct access
=======
// Check to ensure this file is included in Joomla!
>>>>>>> cfd5894... [COM_STOREFRONT] Add support for multiple serial numbers for software downloads
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
<<<<<<< HEAD
						UNIQUE KEY `unique keys for a SKU` (`srNumber`,`srSId`)
=======
  						UNIQUE KEY `unique keys for a SKU` (`srNumber`,`srSId`)
>>>>>>> cfd5894... [COM_STOREFRONT] Add support for multiple serial numbers for software downloads
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
<<<<<<< HEAD
}
=======

}
>>>>>>> cfd5894... [COM_STOREFRONT] Add support for multiple serial numbers for software downloads
