<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script to add meta columns to #__cart_transaction_steps and cart_transaction_items tables
 **/
class Migration20160620171427ComCart extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__cart_transaction_items') && !$this->db->tableHasField('#__cart_transaction_items', 'tiMeta'))
		{
			$query = "ALTER TABLE `#__cart_transaction_items` ADD `tiMeta` text";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__cart_transaction_steps') && !$this->db->tableHasField('#__cart_transaction_steps', 'tsMeta'))
		{
			$query = "ALTER TABLE `#__cart_transaction_steps` ADD `tsMeta` char(255)";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__cart_transaction_items') && $this->db->tableHasField('#__cart_transaction_items', 'tiMeta'))
		{
			$query = "ALTER TABLE `#__cart_transaction_items` DROP COLUMN `tiMeta`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__cart_transaction_steps') && $this->db->tableHasField('#__cart_transaction_steps', 'tsMeta'))
		{
			$query = "ALTER TABLE `#__cart_transaction_steps` DROP COLUMN `tsMeta`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
