<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding storefront component entry
 **/
class Migration20150729164314ComStorefront extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__storefront_collections') && $this->db->tableHasField('#__storefront_collections', 'cParent')) {
			$query = "ALTER TABLE `#__storefront_collections` MODIFY `cParent` CHAR(1) DEFAULT NULL";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__storefront_product_meta') && $this->db->tableHasField('#__storefront_product_meta', 'pmValue')) {
			$query = "ALTER TABLE `#__storefront_product_meta` MODIFY `pmValue` TEXT";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__storefront_product_types')) {
			$query = "INSERT IGNORE INTO `#__storefront_product_types` (`ptName`, `ptModel`) VALUES ('Software Download', 'software')";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__storefront_collections') && $this->db->tableHasField('#__storefront_collections', 'cParent')) {
			$query = "ALTER TABLE `#__storefront_collections` MODIFY `cParent` INT(16) DEFAULT NULL";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__storefront_product_meta') && $this->db->tableHasField('#__storefront_product_meta', 'pmValue')) {
			$query = "ALTER TABLE `#__storefront_product_meta` MODIFY `pmValue` VARCHAR(255)";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__storefront_product_types')) {
			$query = "DELETE FROM `#__storefront_product_types` WHERE ptName = 'Software Download' AND  ptModel = 'software'";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
