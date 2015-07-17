<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for addind cart component entry
 **/
class Migration20150729164628ComCart extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__cart_transaction_items') && !$this->db->tableHasField('#__cart_transaction_items', 'tiMeta')) {
			$query = "ALTER TABLE `#__cart_transaction_items` ADD `tiMeta` TEXT";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__cart_transaction_steps') && !$this->db->tableHasField('#__cart_transaction_steps', 'tsMeta')) {
			$query = "ALTER TABLE `#__cart_transaction_steps` ADD `tsMeta` CHAR";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__cart_transaction_items') && $this->db->tableHasField('#__cart_transaction_items', 'tiMeta')) {
			$query = "ALTER TABLE `#__cart_transaction_items` DROP COLUMN `tiMeta`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__cart_transaction_steps') && $this->db->tableHasField('#__cart_transaction_steps', 'tsMeta')) {
			$query = "ALTER TABLE `#__cart_transaction_steps` DROP COLUMN `tsMeta`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}