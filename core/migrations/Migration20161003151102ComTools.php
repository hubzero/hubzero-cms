<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for expire-session daemon, to record end time for jobs automatically with a timestamp
 **/
class Migration20161003151102ComTools extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		// ADD COLUMN end
		if ($this->db->tableExists('joblog') && !$this->db->tableHasField('joblog', 'end')) {
			$query = "ALTER TABLE joblog ADD COLUMN end TIMESTAMP DEFAULT CURRENT_TIMESTAMP;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		// Drop column end
		if ($this->db->tableExists('joblog') && $this->db->tableHasField('joblog', 'end')) {
			$query = "ALTER TABLE joblog DROP COLUMN end;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
