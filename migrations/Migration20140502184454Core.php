<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

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