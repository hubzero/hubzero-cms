<?php

use Hubzero\Content\Migration\Base;

// No direct access
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
						UNIQUE KEY `single entry per item` (`scope`,`scope_id`,`uId`)
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
}
