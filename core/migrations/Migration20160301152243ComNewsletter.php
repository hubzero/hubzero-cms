<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding the autogen field to the table
 **/
class Migration20160301152243ComNewsletter extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__newsletters') && !$this->db->tableHasField('#__newsletters', 'autogen'))
		{
			$query = "ALTER TABLE `#__newsletters` ADD `autogen` INT(11) DEFAULT '0'";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__newsletters') && $this->db->tableHasField('#__newsletters', 'autogen'))
		{
			$query = "ALTER TABLE `#__newsletters` DROP COLUMN `autogen`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
