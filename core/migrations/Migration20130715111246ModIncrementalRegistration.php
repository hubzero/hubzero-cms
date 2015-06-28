<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding mail preference option to incremental registration
 **/
class Migration20130715111246ModIncrementalRegistration extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableHasField('#__profile_completion_awards', 'mailPreferenceOption'))
		{
			$query = "ALTER TABLE `#__profile_completion_awards` ADD COLUMN mailPreferenceOption int not null default 0;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		$query = "SELECT * FROM `#__incremental_registration_labels` WHERE `field` = 'mailPreferenceOption';";
		$this->db->setQuery($query);
		if (!$this->db->loadResult())
		{
			$query = "INSERT INTO `#__incremental_registration_labels` (field, label) VALUES ('mailPreferenceOption', 'E-Mail Updates');";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableHasField('#__profile_completion_awards', 'mailPreferenceOption'))
		{
			$query = "ALTER TABLE `#__profile_completion_awards` DROP COLUMN mailPreferenceOption;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		$query = "DELETE FROM `#__incremental_registration_labels` WHERE `field` = 'mailPreferenceOption';";
		$this->db->setQuery($query);
		$this->db->query();
	}
}
