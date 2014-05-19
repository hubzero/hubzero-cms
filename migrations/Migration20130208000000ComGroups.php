<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

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
