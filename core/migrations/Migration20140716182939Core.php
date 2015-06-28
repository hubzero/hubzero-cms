<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding scope field to migrations table
 **/
class Migration20140716182939Core extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__migrations') && !$this->db->tableHasField('#__migrations', 'scope'))
		{
			$query = "ALTER TABLE `#__migrations` ADD `scope` VARCHAR(255) NOT NULL DEFAULT '' AFTER `file`";
			$this->db->setQuery($query);
			$this->db->query();
			$query = "UPDATE `#__migrations` SET `scope` = " . $this->db->quote(PATH_ROOT . DS . 'migrations');
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('migrations') && !$this->db->tableHasField('migrations', 'scope'))
		{
			$query = "ALTER TABLE `migrations` ADD `scope` VARCHAR(255) NOT NULL DEFAULT '' AFTER `file`";
			$this->db->setQuery($query);
			$this->db->query();
			$query = "UPDATE `migrations` SET `scope` = " . $this->db->quote(PATH_ROOT . DS . 'migrations');
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__migrations') && $this->db->tableHasField('#__migrations', 'scope'))
		{
			$query = "ALTER TABLE `#__migrations` DROP `scope`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('migrations') && $this->db->tableHasField('migrations', 'scope'))
		{
			$query = "ALTER TABLE `migrations` DROP `scope`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}