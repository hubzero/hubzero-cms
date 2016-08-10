<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script to add AUTO_INCREMENT to pcId
 **/
class Migration20150924000002ComStorefront extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__storefront_product_collections') && $this->db->tableHasField('#__storefront_product_collections', 'pcId'))
		{
			$query = "ALTER TABLE `#__storefront_product_collections` MODIFY COLUMN `pcId` INT(16) NOT NULL AUTO_INCREMENT";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
