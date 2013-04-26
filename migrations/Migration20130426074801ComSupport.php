<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for add watching table
 **/
class Migration20130426074801ComSupport extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		$query = "";

		if (!$db->tableExists('#__support_watching'))
		{
			$query .= "CREATE TABLE `#__support_watching` (
							`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
							`ticket_id` int(11) NOT NULL DEFAULT '0',
							`user_id` int(11) NOT NULL DEFAULT '0',
							PRIMARY KEY (`id`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		}

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}

		$query = "";

		if (!$db->tableHasKey('#__support_watching', 'idx_ticket_id'))
		{
			$query .= "ALTER TABLE `#__support_watching` ADD INDEX `idx_ticket_id` (`ticket_id`);";
		}

		if (!$db->tableHasKey('#__support_watching', 'idx_user_id'))
		{
			$query .= "ALTER TABLE `#__support_watching` ADD INDEX `idx_user_id` (`user_id`);";
		}

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		$query = "";

		if ($db->tableExists('#__support_watching'))
		{
			$query .= "DROP TABLE `#__support_watching`";
		}

		if ($db->tableHasKey('#__support_watching', 'idx_ticket_id'))
		{
			$query .= "ALTER TABLE DROP INDEX `idx_ticket_id`;";
		}

		if ($db->tableHasKey('#__support_watching', 'idx_user_id'))
		{
			$query .= "ALTER TABLE DROP INDEX `idx_user_id`;";
		}

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}
}