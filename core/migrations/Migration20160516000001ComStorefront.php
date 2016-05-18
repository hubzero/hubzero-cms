<?php

use Hubzero\Content\Migration\Base;

<<<<<<< HEAD
// No direct access
=======
// Check to ensure this file is included in Joomla!
>>>>>>> 710976c... [COM_STOREFRONT] Add restricted access to SKU. Ability to import CSV with users authorized to purchase SKUs
defined('_HZEXEC_') or die();

/**
 * Migration script to add SKU restrictions by users
 **/
class Migration20160516000001ComStorefront extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__storefront_skus') && !$this->db->tableHasField('#__storefront_skus', 'sRestricted'))
		{
			$query = "ALTER TABLE `#__storefront_skus` ADD `sRestricted` tinyint(1) DEFAULT '0'";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if (!$this->db->tableExists('#__storefront_permissions'))
		{
			$query = "	CREATE TABLE `#__storefront_permissions` (
						`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
						`scope` varchar(15) DEFAULT NULL,
						`scope_id` int(11) DEFAULT NULL,
						`uId` int(11) DEFAULT NULL,
						PRIMARY KEY (`id`),
<<<<<<< HEAD
						UNIQUE KEY `single entry per item` (`scope`,`scope_id`,`uId`)
=======
  						UNIQUE KEY `single entry per item` (`scope`,`scope_id`,`uId`)
>>>>>>> 710976c... [COM_STOREFRONT] Add restricted access to SKU. Ability to import CSV with users authorized to purchase SKUs
						) ENGINE=MyISAM DEFAULT CHARSET=utf8";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

<<<<<<< HEAD
	/**
	 * Down
	 **/
=======
>>>>>>> 710976c... [COM_STOREFRONT] Add restricted access to SKU. Ability to import CSV with users authorized to purchase SKUs
	public function down()
	{
		if ($this->db->tableExists('#__storefront_skus') && $this->db->tableHasField('#__storefront_skus', 'sRestricted'))
		{
			$query = "ALTER TABLE `#__storefront_skus` DROP COLUMN `sRestricted`";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableExists('#__storefront_permissions'))
		{
			$query = "DROP TABLE `#__storefront_permissions`";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}
<<<<<<< HEAD
=======

>>>>>>> 710976c... [COM_STOREFRONT] Add restricted access to SKU. Ability to import CSV with users authorized to purchase SKUs
}