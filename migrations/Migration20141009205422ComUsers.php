<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for fixing index name on users transactions table
 **/
class Migration20141009205422ComUsers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__users_transactions'))
		{
			if ($this->db->tableHasKey('#__users_transactions', 'idx_referenceid_categroy_type'))
			{
				$query = "ALTER TABLE `#__users_transactions` DROP INDEX `idx_referenceid_categroy_type`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__users_transactions', 'jos_users_transactions_referenceid_categroy_type_idx'))
			{
				$query = "ALTER TABLE `#__users_transactions` DROP INDEX `jos_users_transactions_referenceid_categroy_type_idx`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__users_transactions', 'idx_referenceid_category_type'))
			{
				$query = "ALTER TABLE `#__users_transactions` ADD INDEX `idx_referenceid_category_type` (`referenceid`,`category`,`type`)";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}