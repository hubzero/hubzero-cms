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
 * Migration script for adding unique constraint to tool version zone
 **/
class Migration20140716180300ComTools extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__tool_version_zone') && !$this->db->tableHasKey('#__tool_version_zone', 'idx_zoneid_toolversionid'))
		{
			$query = "ALTER TABLE `#__tool_version_zone` ADD CONSTRAINT UNIQUE KEY `idx_zoneid_toolversionid`(zone_id, tool_version_id)";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__tool_version_zone') && $this->db->tableHasKey('#__tool_version_zone', 'idx_zoneid_toolversionid'))
		{
			$query = "ALTER TABLE `#__tool_version_zone` DROP KEY `idx_zoneid_toolversionid`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
