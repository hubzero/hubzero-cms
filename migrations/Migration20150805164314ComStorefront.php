<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding storefront products access field to support access levels
 **/
class Migration20150805164314ComStorefront extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__storefront_products') && !$this->db->tableHasField('#__storefront_products', 'access')) {
			$query = "ALTER TABLE `#__storefront_products` ADD `access` TINYINT(3)";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__storefront_products') && $this->db->tableHasField('#__storefront_products', 'access')) {
			$query = "ALTER TABLE `#__storefront_products` DROP COLUMN `access`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}