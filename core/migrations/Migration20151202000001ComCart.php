<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding projects tables to support filesystem connections
 **/
class Migration20151202000001ComCart extends Base
{
	public function up()
	{
		if ($this->db->tableExists('#__cart_downloads') && !$this->db->tableHasField('#__cart_downloads', 'dIp'))
		{
			$query = "ALTER TABLE `#__cart_downloads` ADD `dIp` INT UNSIGNED";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	public function down()
	{
		if ($this->db->tableExists('#__cart_downloads') && $this->db->tableHasField('#__cart_downloads', 'dIp'))
		{
			$query = "ALTER TABLE `#__cart_downloads` DROP COLUMN `dIp`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}