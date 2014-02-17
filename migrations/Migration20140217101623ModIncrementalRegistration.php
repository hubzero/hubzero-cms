<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for ...
 **/
class Migration20140217101623ModIncrementalRegistration extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__incremental_registration_labels'))
		{
			$query = "INSERT INTO `#__incremental_registration_labels`(field, label) VALUES ('location', 'Postal Code')";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__xprofiles') && !$this->db->tableHasField('#__xprofiles', 'location'))
		{
			$query = "ALTER TABLE `#__xprofiles` ADD COLUMN `location` VARCHAR(50)";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__profile_completion_awards') && !$this->db->tableHasField('#__profile_completion_awards', 'location'))
		{
			$query = "ALTER TABLE `#__profile_completion_awards` ADD COLUMN `location` TINYINT NOT NULL DEFAULT 0";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__incremental_registration_labels'))
		{
			$query = "DELETE FROM `#__incremental_registration_labels` WHERE field = 'location'";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__xprofiles') && $this->db->tableHasField('#__xprofiles', 'location'))
		{
			$query = "ALTER TABLE `#__xprofiles` DROP COLUMN `location`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__profile_completion_awards') && $this->db->tableHasField('#__profile_completion_awards', 'location'))
		{
			$query = "ALTER TABLE `#__profile_completion_awards` DROP COLUMN `location`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
