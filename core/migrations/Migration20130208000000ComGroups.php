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
 * Migration script for privacy/access cleanup in xgroups table
 **/
class Migration20130208000000ComGroups extends Base
{
	public function up()
	{
		$query = '';

		if ($this->db->tableHasField('#__xgroups', 'access'))
		{
			$query .= "ALTER TABLE `#__xgroups` DROP `access`;\n";
		}
		if ($this->db->tableHasField('#__xgroups', 'privacy') && !$this->db->tableHasField('#__xgroups', 'discoverability'))
		{
			$query .= "ALTER TABLE `#__xgroups` CHANGE `privacy` `discoverability` TINYINT(3);\n";
		}
		if (!$this->db->tableHasField('#__xgroups', 'approved'))
		{
			$query .= "ALTER TABLE `#__xgroups` ADD COLUMN `approved` TINYINT(3) DEFAULT 1 AFTER `published`;";
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

		if ($this->db->tableHasField('#__xgroups', 'approved'))
		{
			$query .= "ALTER TABLE `#__xgroups` DROP `approved`;\n";
		}
		if (!$this->db->tableHasField('#__xgroups', 'privacy') && $this->db->tableHasField('#__xgroups', 'discoverability'))
		{
			$query .= "ALTER TABLE `#__xgroups` CHANGE `discoverability` `privacy` TINYINT(3);\n";
		}
		if (!$this->db->tableHasField('#__xgroups', 'access'))
		{
			$query .= "ALTER TABLE `#__xgroups` ADD COLUMN `access` tinyint(3) DEFAULT '0' AFTER `type`;";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
