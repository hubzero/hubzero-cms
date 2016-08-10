<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script to create the downloads log table
 **/
class Migration20150923000001ComStorefront extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__storefront_collections') && !$this->db->tableHasField('#__storefront_collections', 'cAlias'))
		{
			$query = "ALTER TABLE `#__storefront_collections` ADD `cAlias` CHAR(50) DEFAULT NULL";
			$this->db->setQuery($query);
			$this->db->query();
		}


		$query = "ALTER TABLE `#__storefront_collections` MODIFY `cId` INT(16) UNSIGNED NOT NULL AUTO_INCREMENT";
		$this->db->setQuery($query);
		$this->db->query();

		if ($this->db->tableExists('#__storefront_product_collections') && $this->db->tableHasField('#__storefront_product_collections', 'cllId'))
		{
			$query = "ALTER TABLE `#__storefront_product_collections` CHANGE COLUMN `cllId` `pcId` INT(16) NOT NULL AUTO_INCREMENT;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	public function down()
	{
		if ($this->db->tableExists('#__storefront_collections') && $this->db->tableHasField('#__storefront_collections', 'cAlias'))
		{
			$query = "ALTER TABLE `#__storefront_collections` DROP COLUMN `cAlias`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		$query = "ALTER TABLE `#__storefront_collections` MODIFY `cId` CHAR(50) DEFAULT NULL;";
		$this->db->setQuery($query);
		$this->db->query();

		if ($this->db->tableExists('#__storefront_product_collections') && $this->db->tableHasField('#__storefront_product_collections', 'pcId'))
		{
			$query = "ALTER TABLE `#__storefront_product_collections` CHANGE COLUMN `pcId` `cllId` INT(16);";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

}
