<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script to add AUTO_INCREMENT to cId
 **/
class Migration20151023000001ComStorefront extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__storefront_collections') && $this->db->tableHasField('#__storefront_collections', 'cId'))
		{
			$query = "ALTER TABLE `#__storefront_collections` MODIFY COLUMN `cId` INT(16) UNSIGNED NOT NULL AUTO_INCREMENT";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	public function down()
	{

	}
}
