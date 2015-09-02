<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding expiredate column to jobs table 
 **/
class Migration20150902133259ComJobs extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__jobs_openings')
				&& !$this->db->tableHasField('#__jobs_openings', 'expiredate'))
		{
			$query = "ALTER TABLE `#__jobs_openings` ADD COLUMN `expiredate`
			DATETIME NULL DEFAULT '0000-00-00 00:00:00' AFTER closedate;";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__jobs_openings')
				&& $this->db->tableHasField('#__jobs_openings', 'expiredate'))
		{
			$query = "ALTER TABLE `#__jobs_openings` DROP COLUMN `expiredate`;";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
