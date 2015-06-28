<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding venue_id to host table
 **/
class Migration20130214000000Core extends Base
{
	public function up()
	{
		$query = '';

		if (!$this->db->tableHasField('host', 'venue_id'))
		{
			$query .= "ALTER TABLE `host` ADD COLUMN `venue_id` INT(11)  AFTER `portbase`;";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	public function down()
	{
		$query = '';

		if ($this->db->tableHasField('host', 'venue_id'))
		{
			$query .= "ALTER TABLE `host` DROP COLUMN `venue_id`;";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
