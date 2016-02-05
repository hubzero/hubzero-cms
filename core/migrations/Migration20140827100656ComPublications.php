<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script to add group_owner field to #__publications table
 **/
class Migration20140827100656ComPublications extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__publications')
			&& !$this->db->tableHasField('#__publications', 'group_owner'))
		{
			$query = "ALTER TABLE `#__publications` ADD `group_owner` int(11) NOT NULL DEFAULT '0'";
			$this->db->setQuery($query);
			$this->db->query();
		}

	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__publications') && $this->db->tableHasField('#__publications', 'group_owner'))
		{
			$query = "ALTER TABLE `#__publications` DROP `group_owner`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}