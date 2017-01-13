<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script to add #__cart_downloads table
 **/
class Migration20170112000001ComStorefront extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableHasField('#__storefront_serials', 'srNumber'))
		{
			$query = "ALTER TABLE `#__storefront_serials` MODIFY `srNumber` VARCHAR(255);";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableHasField('#__storefront_serials', 'srNumber'))
		{
			$query = "ALTER TABLE `#__storefront_serials` MODIFY `srNumber` VARCHAR(32);";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
