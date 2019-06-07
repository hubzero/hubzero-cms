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
 * Migration script for updating index for field that changed names
 **/
class Migration20141030163700ComBlog extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__blog_entries'))
		{
			if ($this->db->tableHasKey('#__blog_entries', 'idx_group_id') && !$this->db->tableHasField('#__blog_entries', 'group_id'))
			{
				$query = "ALTER TABLE `#__blog_entries` DROP INDEX `idx_group_id`;";
				$this->db->setQuery($query);
				$this->db->query();

				if ($this->db->tableHasField('#__blog_entries', 'scope_id'))
				{
					$query = "ALTER TABLE `#__blog_entries` ADD INDEX `idx_scope_id` (`scope_id`);";
					$this->db->setQuery($query);
					$this->db->query();
				}
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__blog_entries'))
		{
			if ($this->db->tableHasKey('#__blog_entries', 'idx_scope_id'))
			{
				$query = "ALTER TABLE `#__blog_entries` DROP INDEX `idx_scope_id`;";
				$this->db->setQuery($query);
				$this->db->query();

				if ($this->db->tableHasField('#__blog_entries', 'group_id'))
				{
					$query = "ALTER TABLE `#__blog_entries` ADD INDEX `idx_group_id` (`group_id`);";
					$this->db->setQuery($query);
					$this->db->query();
				}
			}
		}
	}
}
