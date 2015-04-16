<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding default zone field
 **/
class Migration20150416104000ComWishlist extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__wishlist'))
		{
			if (!$this->db->tableHasField('#__wishlist', 'notify'))
			{
				$query = "ALTER TABLE `#__wishlist` ADD COLUMN `notify` tinyint(1) DEFAULT '1'";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}


	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__wishlist'))
		{
			if ($this->db->tableHasField('#__wishlist', 'notify'))
			{
				$query = "ALTER TABLE `#__wishlist` DROP COLUMN `notify`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}