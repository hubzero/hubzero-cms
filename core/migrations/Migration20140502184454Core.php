<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for renaming migrations table
 **/
class Migration20140502184454Core extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('migrations') && !$this->db->tableExists('#__migrations'))
		{
			$query = "RENAME TABLE `migrations` TO `#__migrations`";
			$this->db->setQuery($query);
			$this->db->query();

			$this->callback('migration', 'setTableName', array('#__migrations'));
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if (!$this->db->tableExists('migrations') && $this->db->tableExists('#__migrations'))
		{
			$query = "RENAME TABLE `#__migrations` TO `migrations`";
			$this->db->setQuery($query);
			$this->db->query();

			$this->callback('migration', 'setTableName', array('migrations'));
		}
	}
}