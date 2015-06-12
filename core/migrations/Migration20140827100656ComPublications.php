<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script to add group_owner field to jos_publications table
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