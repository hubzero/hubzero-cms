<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding indices to xmessage tables
 **/
class Migration20140829131400PlgMembersMessages extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__xmessage'))
		{
			$query = "ALTER TABLE `#__xmessage`
					CHANGE `id` `id` INT(11)  UNSIGNED  NOT NULL  AUTO_INCREMENT,
					CHANGE `group_id` `group_id` INT(11)  UNSIGNED  NOT NULL  DEFAULT '0',
					CHANGE `created_by` `created_by` INT(11)  UNSIGNED  NOT NULL  DEFAULT '0'
			;";
			$this->db->setQuery($query);
			$this->db->query();

			if (!$this->db->tableHasKey('#__xmessage', 'idx_component'))
			{
				$query = "ALTER TABLE `#__xmessage` ADD INDEX `idx_component` (`component`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__xmessage', 'idx_group_id'))
			{
				$query = "ALTER TABLE `#__xmessage` ADD INDEX `idx_group_id` (`group_id`);";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__xmessage_component'))
		{
			if (!$this->db->tableHasKey('#__xmessage_component', 'idx_component'))
			{
				$query = "ALTER TABLE `#__xmessage_component` ADD INDEX `idx_component` (`component`);";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__xmessage_notify'))
		{
			$query = "ALTER TABLE `#__xmessage_notify`
					CHANGE `id` `id` INT(11)  UNSIGNED  NOT NULL  AUTO_INCREMENT,
					CHANGE `uid` `uid` INT(11)  UNSIGNED  NOT NULL  DEFAULT '0'
			;";
			$this->db->setQuery($query);
			$this->db->query();

			if (!$this->db->tableHasKey('#__xmessage_notify', 'idx_uid'))
			{
				$query = "ALTER TABLE `#__xmessage_notify` ADD INDEX `idx_uid` (`uid`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__xmessage_notify', 'idx_method'))
			{
				$query = "ALTER TABLE `#__xmessage_notify` ADD INDEX `idx_method` (`method`);";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__xmessage_seen'))
		{
			if ($this->db->tableHasKey('#__xmessage_seen', 'uid'))
			{
				$query = "ALTER TABLE `#__xmessage_seen` DROP INDEX `uid`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__xmessage_seen', 'idx_uid'))
			{
				$query = "ALTER TABLE `#__xmessage_seen` ADD INDEX `idx_uid` (`uid`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__xmessage_seen', 'mid'))
			{
				$query = "ALTER TABLE `#__xmessage_seen` DROP INDEX `mid`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__xmessage_seen', 'idx_mid'))
			{
				$query = "ALTER TABLE `#__xmessage_seen` ADD INDEX `idx_mid` (`mid`);";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__xmessage'))
		{
			if ($this->db->tableHasKey('#__xmessage', 'idx_component'))
			{
				$query = "ALTER TABLE `#__xmessage` DROP INDEX `idx_component`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__xmessage', 'idx_group_id'))
			{
				$query = "ALTER TABLE `#__xmessage` DROP INDEX `idx_group_id`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__xmessage_component'))
		{
			if ($this->db->tableHasKey('#__xmessage_component', 'idx_component'))
			{
				$query = "ALTER TABLE `#__xmessage_component` DROP INDEX `idx_component`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__xmessage_notify'))
		{
			if ($this->db->tableHasKey('#__xmessage_notify', 'idx_uid'))
			{
				$query = "ALTER TABLE `#__xmessage_notify` DROP INDEX `idx_uid`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__xmessage_notify', 'idx_method'))
			{
				$query = "ALTER TABLE `#__xmessage_notify` DROP INDEX `idx_method`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__xmessage_seen'))
		{
			if ($this->db->tableHasKey('#__xmessage_seen', 'idx_uid'))
			{
				$query = "ALTER TABLE `#__xmessage_seen` DROP INDEX `idx_uid`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__xmessage_seen', 'uid'))
			{
				$query = "ALTER TABLE `#__xmessage_seen` ADD INDEX `uid` (`uid`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__xmessage_seen', 'idx_mid'))
			{
				$query = "ALTER TABLE `#__xmessage_seen` DROP INDEX `idx_mid`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__xmessage_seen', 'mid'))
			{
				$query = "ALTER TABLE `#__xmessage_seen` ADD INDEX `mid` (`mid`);";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}