<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for courses member notes indices
 **/
class Migration20130410000000ComCourses extends Base
{
	public function up()
	{
		$query = '';

		if (!$this->db->tableHasKey('#__courses_member_notes', 'idx_scoped'))
		{
			$query .= "ALTER TABLE `#__courses_member_notes` ADD INDEX `idx_scoped` (`scope`, `scope_id`);\n";
		}
		if (!$this->db->tableHasKey('#__courses_member_notes', 'idx_createdby'))
		{
			$query .= "ALTER TABLE `#__courses_member_notes` ADD INDEX `idx_createdby` (`created_by`);";
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

		if ($this->db->tableHasKey('#__courses_member_notes', 'idx_scoped'))
		{
			$query .= "DROP INDEX `idx_scoped` ON `#__courses_member_notes`;\n";
		}
		if ($this->db->tableHasKey('#__courses_member_notes', 'idx_createdby'))
		{
			$query .= "DROP INDEX `idx_createdby` ON `#__courses_member_notes`;";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
