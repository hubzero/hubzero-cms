<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding mail preference option to incremental registration
 **/
class Migration20130715111246ModIncrementalRegistration extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		if (!$db->tableHasField('#__profile_completion_awards', 'mailPreferenceOption'))
		{
			$query = "ALTER TABLE `#__profile_completion_awards` ADD COLUMN mailPreferenceOption int not null default 0;";
			$db->setQuery($query);
			$db->query();
		}

		$query = "SELECT * FROM `#__incremental_registration_labels` WHERE `field` = 'mailPreferenceOption';";
		$db->setQuery($query);
		if (!$db->loadResult())
		{
			$query = "INSERT INTO `#__incremental_registration_labels` (field, label) VALUES ('mailPreferenceOption', 'E-Mail Updates');";
			$db->setQuery($query);
			$db->query();
		}
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		if ($db->tableHasField('#__profile_completion_awards', 'mailPreferenceOption'))
		{
			$query = "ALTER TABLE `#__profile_completion_awards` DROP COLUMN mailPreferenceOption;";
			$db->setQuery($query);
			$db->query();
		}

		$query = "DELETE FROM `#__incremental_registration_labels` WHERE `field` = 'mailPreferenceOption';";
		$db->setQuery($query);
		$db->query();
	}
}
