<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding projects tables to support filesystem connections
 **/
class Migration20151116000001ComCart extends Base
{
	public function up()
	{
		if ($this->db->tableExists('#__cart_downloads') && !$this->db->tableHasField('#__cart_downloads', 'dStatus'))
		{
			$query = "ALTER TABLE `#__cart_downloads` ADD `dStatus` TINYINT(1) DEFAULT '1'";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	public function down()
	{
		if ($this->db->tableExists('#__cart_downloads') && $this->db->tableHasField('#__cart_downloads', 'dStatus'))
		{
			$query = "ALTER TABLE `#__cart_downloads` DROP COLUMN `dStatus`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}