<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding 'username', 'email', 'disability' columns to profile_completion_award table
 **/
class Migration20171106183501ComMembers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__profile_completion_awards'))
		{
			if (!$this->db->tableHasField('#__profile_completion_awards', 'username'))
			{
				$query = "ALTER TABLE `#__profile_completion_awards` ADD `username` TINYINT(4)  NOT NULL  DEFAULT '0';";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasField('#__profile_completion_awards', 'email'))
			{
				$query = "ALTER TABLE `#__profile_completion_awards` ADD `email` TINYINT(4)  NOT NULL  DEFAULT '0';";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasField('#__profile_completion_awards', 'disability'))
			{
				$query = "ALTER TABLE `#__profile_completion_awards` ADD `disability` TINYINT(4)  NOT NULL  DEFAULT '0';";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__profile_completion_awards'))
		{
			if ($this->db->tableHasField('#__profile_completion_awards', 'username'))
			{
				$query = "ALTER TABLE `#__profile_completion_awards` DROP `username`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__profile_completion_awards', 'email'))
			{
				$query = "ALTER TABLE `#__profile_completion_awards` DROP `email`';";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__profile_completion_awards', 'disability'))
			{
				$query = "ALTER TABLE `#__profile_completion_awards` DROP `disability`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
