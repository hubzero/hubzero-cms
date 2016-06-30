<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script to add table for tracking product/access group relations
 **/
class Migration20160630134818ComStorefront extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__storefront_product_access_groups'))
		{
			$query = "CREATE TABLE `#__storefront_product_access_groups` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `pId` int(11) NOT NULL DEFAULT '0',
			  `agId` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_pId` (`pId`),
			  KEY `idx_agId` (`agId`)
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
		if ($this->db->tableExists('#__storefront_product_access_groups'))
		{
			$query = "DROP TABLE `#__storefront_product_access_groups`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
