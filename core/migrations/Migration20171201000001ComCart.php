<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script to rename production_collections primary key
 **/
class Migration20171201000001ComCart extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__cart_transaction_info'))
		{
			if (!$this->db->tableHasField('#__cart_transaction_info', 'tiPayment'))
			{
				$query = "ALTER TABLE `#__cart_transaction_info` ADD COLUMN `tiPayment` CHAR(30), ADD COLUMN `tiPaymentDetails` CHAR(255);";
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
		if ($this->db->tableExists('#__cart_transaction_info'))
		{
			if ($this->db->tableHasField('#__cart_transaction_info', 'tiPayment'))
			{
				$query = "ALTER TABLE `#__cart_transaction_info` DROP COLUMN `tiPayment`, DROP COLUMN `tiPaymentDetails`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
