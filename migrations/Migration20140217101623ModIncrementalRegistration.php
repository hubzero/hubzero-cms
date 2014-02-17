<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for ...
 **/
class Migration20140217101623ModIncrementalRegistration extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		foreach (array(
				'INSERT INTO #__incremental_registration_labels(field, label) VALUES (\'location\', \'Postal Code\')',
				'ALTER TABLE #__xprofiles ADD COLUMN `location` VARCHAR(50)',
				'ALTER TABLE #__profile_completion_awards ADD COLUMN `location` TINYINT NOT NULL DEFAULT 0'
			) as $query) {
			$db->setQuery($query);
			$db->query();
		}
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		foreach (array(
				'DELETE FROM #__incremental_registration_labels WHERE field = \'location\'',
				'ALTER TABLE #__xprofiles DROP COLUMN `location`',
				'ALTER TABLE #__profile_completion_awards DROP COLUMN `location`'
			) as $query) {
			$db->setQuery($query);
			$db->query();
		}
	}
}
