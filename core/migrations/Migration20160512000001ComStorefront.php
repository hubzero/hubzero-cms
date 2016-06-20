<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script to add publish_up and publish_down fields to Products and SKUs tables
 **/
class Migration20160512000001ComStorefront extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__storefront_products') && !$this->db->tableHasField('#__storefront_products', 'publish_up'))
		{
			$query = "ALTER TABLE `#__storefront_products` ADD `publish_up` DATETIME, ADD `publish_down` DATETIME";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableExists('#__storefront_skus') && !$this->db->tableHasField('#__storefront_skus', 'publish_up'))
		{
			$query = "ALTER TABLE `#__storefront_skus` ADD `publish_up` DATETIME, ADD `publish_down` DATETIME";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__storefront_products') && $this->db->tableHasField('#__storefront_products', 'publish_up'))
		{
			$query = "ALTER TABLE `#__storefront_products` DROP COLUMN `publish_up`, DROP COLUMN `publish_down`";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableExists('#__storefront_skus') && $this->db->tableHasField('#__storefront_skus', 'publish_up'))
		{
			$query = "ALTER TABLE `#__storefront_skus` DROP COLUMN `publish_up`, DROP COLUMN `publish_down`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
