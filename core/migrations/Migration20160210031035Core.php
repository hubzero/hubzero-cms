<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding a status column to the migrations table
 **/
class Migration20160210031035Core extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__migrations')
		&& !$this->db->tableHasField('#__migrations', 'status')
		&& $this->db->tableHasField('#__migrations', 'action_by'))
		{
			$query = "ALTER TABLE `#__migrations` ADD `status` varchar(255) NOT NULL DEFAULT '' AFTER `action_by`";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "UPDATE `#__migrations` SET `status` = 'success'";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__migrations') && $this->db->tableHasField('#__migrations', 'status'))
		{
			// We delete all non success entries - this loses data, but it
			// restores the original intent of the migrations table.
			// Otherwise, you'd have entries in the table that will look like 
			// successful runs, even though they weren't.
			$query = "DELETE FROM `#__migrations` WHERE `status` != 'success'";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "ALTER TABLE `#__migrations` DROP `status`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
