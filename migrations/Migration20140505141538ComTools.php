<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for renaming venue_id to zone_id on host table
 **/
class Migration20140505141538ComTools extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('host')
			&& $this->db->tableHasField('host', 'venue_id')
			&& !$this->db->tableHasField('host', 'zone_id'))
		{
			$query = "ALTER TABLE `host` CHANGE `venue_id` `zone_id` INT(11) NULL DEFAULT NULL";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('host')
			&& !$this->db->tableHasField('host', 'venue_id')
			&& $this->db->tableHasField('host', 'zone_id'))
		{
			$query = "ALTER TABLE `host` CHANGE `zone_id` `venue_id` INT(11) NULL DEFAULT NULL";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}