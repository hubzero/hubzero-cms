<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for updating tables for code refactoring based on new models
 **/
class Migration20150109190645ComTime extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		// Rename table fields to work with new models structure
		if ($this->db->tableExists('#__time_tasks'))
		{
			if ($this->db->tableHasField('#__time_tasks', 'liaison')
			&& !$this->db->tableHasField('#__time_tasks', 'liaison_id'))
			{
				$query = "ALTER TABLE `#__time_tasks` CHANGE `liaison` `liaison_id` INT(11) NULL DEFAULT NULL";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__time_tasks', 'assignee')
			&& !$this->db->tableHasField('#__time_tasks', 'assignee_id'))
			{
				$query = "ALTER TABLE `#__time_tasks` CHANGE `assignee` `assignee_id` INT(11) NULL DEFAULT NULL";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		// Create new table #__time_liaisons
		if (!$this->db->tableExists('#__time_liaisons'))
		{
			$query = "CREATE TABLE `#__time_liaisons` (
						`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
						`user_id` int(11) DEFAULT NULL,
						PRIMARY KEY (`id`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8";
			$this->db->setQuery($query);
			$this->db->query();

			// Add rows to table from old time_users table
			$query = "SELECT * FROM `#__time_users` WHERE `liaison` = 1";
			$this->db->setQuery($query);
			$liaisons = $this->db->loadObjectList();

			if ($liaisons && count($liaisons) > 0)
			{
				foreach ($liaisons as $liaison)
				{
					$query = "INSERT INTO `#__time_liaisons` (`user_id`) VALUES (" . $this->db->quote($liaison->user_id) . ")";
					$this->db->setQuery($query);
					$this->db->query();
				}
			}
		}

		// Drop liaison column from time_users table
		if ($this->db->tableExists('#__time_users') && $this->db->tableHasField('#__time_users', 'liaison'))
		{
			$query = "ALTER TABLE `#__time_users` DROP `liaison`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// Rename time_users table to time_managers table
		if ($this->db->tableExists('#__time_users') && !$this->db->tableExists('#__time_proxies'))
		{
			$query = "RENAME TABLE `#__time_users` TO `#__time_proxies`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__time_proxies')
		&&  $this->db->tableHasField('#__time_proxies', 'manager_id')
		&& !$this->db->tableHasField('#__time_proxies', 'proxy_id'))
		{
			$query = "ALTER TABLE `#__time_proxies` CHANGE `manager_id` `proxy_id` INT(11) NULL DEFAULT NULL";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// Delete entries formally present just to represent a liaison, not an actually proxy relationship
		if ($this->db->tableExists('#__time_proxies'))
		{
			$query = "DELETE FROM `#__time_proxies` WHERE `proxy_id` = 0";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// Fix up entries in assets table to be singular
		$query = "UPDATE `#__assets` SET `name` = REPLACE(`name`, 'com_time.hubs.', 'com_time.hub.')";
		$this->db->setQuery($query);
		$this->db->query();
	}
}