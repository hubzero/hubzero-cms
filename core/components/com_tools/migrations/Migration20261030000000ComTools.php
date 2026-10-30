<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding a hard timeout, in seconds, to tool versions
 **/
class Migration20261030000000ComTools extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__tool_version') && !$this->db->tableHasField('#__tool_version', 'hardTimeout'))
		{
			$query = "ALTER TABLE `#__tool_version` ADD COLUMN `hardTimeout` int(11) DEFAULT NULL AFTER `vnc_timeout`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__tool_version') && $this->db->tableHasField('#__tool_version', 'hardTimeout'))
		{
			$query = "ALTER TABLE `#__tool_version` DROP COLUMN `hardTimeout`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
