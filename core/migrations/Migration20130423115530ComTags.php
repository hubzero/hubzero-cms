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
 * Migration script for adding tags substitutes index
 **/
class Migration20130423115530ComTags extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query = "";

		if (!$this->db->tableHasKey('#__tags_substitute', 'idx_tag_id'))
		{
			$query .= "ALTER TABLE `#__tags_substitute` ADD INDEX `idx_tag_id` (`tag_id`);";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$query = "";

		if ($this->db->tableHasKey('#__tags_substitute', 'idx_tag_id'))
		{
			$query .= "DROP INDEX `idx_tag_id` ON `#__tags_substitute`;";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
