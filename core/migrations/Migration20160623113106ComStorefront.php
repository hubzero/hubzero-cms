<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script to rename production_collections primary key
 **/
class Migration20160623113106ComStorefront extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__storefront_product_collections') && $this->db->tableHasField('#__storefront_product_collections', 'cllId'))
		{
			$query = "ALTER TABLE `#__storefront_product_collections` CHANGE `cllId` `pcId` int(16) NOT NULL AUTO_INCREMENT";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__storefront_product_collections') && $this->db->tableHasField('#__storefront_product_collections', 'pcId'))
		{
			$query = "ALTER TABLE `#__storefront_product_collections` CHANGE `pcId` `cllId` int(16) NOT NULL AUTO_INCREMENT";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
