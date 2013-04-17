<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class Migration20130410000000ComCourses extends Hubzero_Migration
{
	protected static function up($db)
	{
		$query = "ALTER TABLE `#__courses_member_notes` ADD INDEX `idx_scoped` (`scope`, `scope_id`);
			ALTER TABLE `#__courses_member_notes` ADD INDEX `idx_createdby` (`created_by`);";

		$db->setQuery($query);
		$db->query();
	}

	protected static function down($db)
	{
		$query = "DROP INDEX `idx_scoped` ON `#__courses_member_notes`;
				DROP INDEX `idx_createdby` ON `#__courses_member_notes`;";

		$db->setQuery($query);
		$db->query();
	}
}