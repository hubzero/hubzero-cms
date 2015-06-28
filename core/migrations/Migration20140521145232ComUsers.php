<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding approval field to users table
 **/
class Migration20140521145232ComUsers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__users')
			&& !$this->db->tableHasField('#__users', 'approved')
			&& $this->db->tableHasField('#__users', 'block'))
		{
			$query = "ALTER TABLE `#__users` ADD `approved` TINYINT(4) NOT NULL DEFAULT '2' AFTER `block`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__users') && $this->db->tableHasField('#__users', 'approved'))
		{
			$query = "ALTER TABLE `#__users` DROP `approved`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}