<?php

use Hubzero\Content\Migration;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class Migration20130410000000ComCourses extends Migration
{
	protected static function up($db)
	{
		$query = '';

		if (!$db->tableHasKey('#__courses_member_notes', 'idx_scoped'))
		{
			$query .= "ALTER TABLE `#__courses_member_notes` ADD INDEX `idx_scoped` (`scope`, `scope_id`);\n";
		}
		if (!$db->tableHasKey('#__courses_member_notes', 'idx_createdby'))
		{
			$query .= "ALTER TABLE `#__courses_member_notes` ADD INDEX `idx_createdby` (`created_by`);";
		}

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}

	protected static function down($db)
	{
		$query = '';

		if ($db->tableHasKey('#__courses_member_notes', 'idx_scoped'))
		{
			$query .= "DROP INDEX `idx_scoped` ON `#__courses_member_notes`;\n";
		}
		if ($db->tableHasKey('#__courses_member_notes', 'idx_createdby'))
		{
			$query .= "DROP INDEX `idx_createdby` ON `#__courses_member_notes`;";
		}

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}
}