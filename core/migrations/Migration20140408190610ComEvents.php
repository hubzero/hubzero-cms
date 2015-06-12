<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for changing how event rules are tracked
 **/
class Migration20140408190610ComEvents extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query = "";

		// remove bad fields
		if ($this->db->tableHasField('#__events', 'sid'))
		{
			$query .= "ALTER TABLE `#__events` DROP COLUMN `sid`;";
			$query .= "ALTER TABLE `#__events` DROP COLUMN `color_bar`;";
			$query .= "ALTER TABLE `#__events` DROP COLUMN `useCatColor`;";
			$query .= "ALTER TABLE `#__events` DROP COLUMN `mask`;";
			$query .= "ALTER TABLE `#__events` DROP COLUMN `created_by_alias`;";
			$query .= "ALTER TABLE `#__events` DROP COLUMN `images`;";
			$query .= "ALTER TABLE `#__events` DROP COLUMN `reccurtype`;";
			$query .= "ALTER TABLE `#__events` DROP COLUMN `reccurday`;";
			$query .= "ALTER TABLE `#__events` DROP COLUMN `reccurweekdays`;";
			$query .= "ALTER TABLE `#__events` DROP COLUMN `reccurweeks`;";
			$query .= "ALTER TABLE `#__events` DROP COLUMN `announcement`;";
			$query .= "ALTER TABLE `#__events` DROP COLUMN `ordering`;";
			$query .= "ALTER TABLE `#__events` DROP COLUMN `archived`;";
			$query .= "ALTER TABLE `#__events` DROP COLUMN `access`;";
			$query .= "ALTER TABLE `#__events` DROP COLUMN `hits`;";
		}

		// add new repeating rule
		if (!$this->db->tableHasField('#__events', 'repeating_rule'))
		{
			$query .= "ALTER TABLE `#__events` ADD COLUMN `repeating_rule` VARCHAR(150) AFTER `time_zone`;";
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

		// add bad fields
		if (!$this->db->tableHasField('#__events', 'sid'))
		{
			$query .= "ALTER TABLE `#__events` ADD COLUMN `sid` int(11) NOT NULL DEFAULT '0' AFTER `id` ;";
			$query .= "ALTER TABLE `#__events` ADD COLUMN `color_bar` varchar(8) NOT NULL DEFAULT '' AFTER `extra_info`;";
			$query .= "ALTER TABLE `#__events` ADD COLUMN `useCatColor` tinyint(1) NOT NULL DEFAULT '0' AFTER `color_bar`;";
			$query .= "ALTER TABLE `#__events` ADD COLUMN `mask` int(11) unsigned NOT NULL DEFAULT '0' AFTER `state`;";
			$query .= "ALTER TABLE `#__events` ADD COLUMN `created_by_alias` varchar(100) NOT NULL DEFAULT '' AFTER `created_by`;";
			$query .= "ALTER TABLE `#__events` ADD COLUMN `images` text NOT NULL AFTER `time_zone`;";
			$query .= "ALTER TABLE `#__events` ADD COLUMN `reccurtype` tinyint(1) NOT NULL DEFAULT '0' AFTER `images`;";
			$query .= "ALTER TABLE `#__events` ADD COLUMN `reccurday` varchar(4) NOT NULL DEFAULT '' AFTER `reccurtype`;";
			$query .= "ALTER TABLE `#__events` ADD COLUMN `reccurweekdays` varchar(20) NOT NULL DEFAULT '' AFTER `reccurday`;";
			$query .= "ALTER TABLE `#__events` ADD COLUMN `reccurweeks` varchar(10) NOT NULL DEFAULT '' AFTER `reccurweekdays`;";
			$query .= "ALTER TABLE `#__events` ADD COLUMN `announcement` tinyint(1) NOT NULL DEFAULT '0' AFTER `approved`;";
			$query .= "ALTER TABLE `#__events` ADD COLUMN `ordering` int(11) NOT NULL DEFAULT '0' AFTER `announcement`;";
			$query .= "ALTER TABLE `#__events` ADD COLUMN `archived` tinyint(1) NOT NULL DEFAULT '0' AFTER `ordering`;";
			$query .= "ALTER TABLE `#__events` ADD COLUMN `access` int(11) unsigned NOT NULL DEFAULT '0' AFTER `archived`;";
			$query .= "ALTER TABLE `#__events` ADD COLUMN `hits` int(11) NOT NULL DEFAULT '0' AFTER `access`;";
		}

		// remove new repeating rule
		if ($this->db->tableHasField('#__events', 'repeating_rule'))
		{
			$query .= "ALTER TABLE `#__events` DROP COLUMN `repeating_rule`;";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}