<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for dropping the steps index
 **/
class Migration20150729164629ComCart extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__cart_transaction_steps'))
		{
			$query = "SHOW INDEX FROM `#__cart_transaction_steps` WHERE Key_name = 'tId'";
			$this->db->setQuery($query);
			$this->db->execute();
			if ($this->db->getNumRows() > 0)
			{
				$query = "DROP INDEX `tId` ON `#__cart_transaction_steps`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}

}