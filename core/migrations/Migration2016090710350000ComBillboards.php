<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script to fix character encoding on field (from past schema upgrade)
 **/
class Migration2016090710350000ComBillboards extends Base
{	
	public function up()
	{
		if ($this->db->tableHasField('#__billboards_collections', 'name'))
		{
			$query = "ALTER TABLE `#__billboards_collections` CHANGE COLUMN `name` `name` varchar(255) DEFAULT NULL;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	public function down()
	{
	}
}
