<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for newsfeeds table changes
 **/
class Migration20130924000012ComNewsfeeds extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		$query = "ALTER TABLE `#__newsfeeds` ENGINE = InnoDB;";
		$db->setQuery($query);
		$db->query();

		if ($db->tableHasField('#__newsfeeds', 'id'))
		{
			$query = "ALTER TABLE `#__newsfeeds` CHANGE COLUMN `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT;";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__newsfeeds', 'name'))
		{
			$query = "ALTER TABLE `#__newsfeeds` CHANGE `name` `name` varchar(100) NOT NULL DEFAULT '';";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__newsfeeds', 'alias'))
		{
			$query = "ALTER TABLE `#__newsfeeds` CHANGE COLUMN `alias` `alias` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NOT NULL DEFAULT '';";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__newsfeeds', 'link'))
		{
			$query = "ALTER TABLE `#__newsfeeds` CHANGE `link` `link` varchar(200) NOT NULL DEFAULT '';";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__newsfeeds', 'numarticles'))
		{
			$query = "ALTER TABLE `#__newsfeeds` CHANGE COLUMN `numarticles` `numarticles` INT(10) UNSIGNED NOT NULL DEFAULT '1';";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__newsfeeds', 'cache_time'))
		{
			$query = "ALTER TABLE `#__newsfeeds` CHANGE COLUMN `cache_time` `cache_time` INT(10) UNSIGNED NOT NULL DEFAULT '3600';";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__newsfeeds', 'checked_out'))
		{
			$query = "ALTER TABLE `#__newsfeeds` CHANGE `checked_out` `checked_out` integer(10) UNSIGNED NOT NULL DEFAULT '0';";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__newsfeeds', 'access'))
		{
			$query = "ALTER TABLE `#__newsfeeds` ADD `access` INT(10) UNSIGNED NOT NULL DEFAULT '0';";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__newsfeeds', 'language'))
		{
			$query = "ALTER TABLE `#__newsfeeds` ADD `language` char(7) NOT NULL DEFAULT '';";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__newsfeeds', 'params'))
		{
			$query = "ALTER TABLE `#__newsfeeds` ADD `params` TEXT NOT NULL;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__newsfeeds', 'created'))
		{
			$query = "ALTER TABLE `#__newsfeeds` ADD COLUMN   `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00';";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__newsfeeds', 'created_by'))
		{
			$query = "ALTER TABLE `#__newsfeeds` ADD COLUMN   `created_by` int(10) unsigned NOT NULL DEFAULT '0';";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__newsfeeds', 'created_by_alias'))
		{
			$query = "ALTER TABLE `#__newsfeeds` ADD COLUMN   `created_by_alias` varchar(255) NOT NULL DEFAULT '';";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__newsfeeds', 'modified'))
		{
			$query = "ALTER TABLE `#__newsfeeds` ADD COLUMN   `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00';";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__newsfeeds', 'modified_by'))
		{
			$query = "ALTER TABLE `#__newsfeeds` ADD COLUMN   `modified_by` int(10) unsigned NOT NULL DEFAULT '0';";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__newsfeeds', 'metakey'))
		{
			$query = "ALTER TABLE `#__newsfeeds` ADD COLUMN   `metakey` text NOT NULL;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__newsfeeds', 'metadesc'))
		{
			$query = "ALTER TABLE `#__newsfeeds` ADD COLUMN   `metadesc` text NOT NULL;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__newsfeeds', 'metadata'))
		{
			$query = "ALTER TABLE `#__newsfeeds` ADD COLUMN   `metadata` text NOT NULL;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__newsfeeds', 'xreference'))
		{
			$query = "ALTER TABLE `#__newsfeeds` ADD COLUMN   `xreference` varchar(50) NOT NULL COMMENT 'A reference to enable linkages to external data sets.';";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__newsfeeds', 'publish_up'))
		{
			$query = "ALTER TABLE `#__newsfeeds` ADD COLUMN   `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00';";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__newsfeeds', 'publish_down'))
		{
			$query = "ALTER TABLE `#__newsfeeds` ADD COLUMN   `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00';";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasKey('#__newsfeeds', 'catid'))
		{
			$query = "ALTER TABLE `#__newsfeeds` DROP INDEX `catid`;";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasKey('#__newsfeeds', 'published'))
		{
			$query = "ALTER TABLE `#__newsfeeds` DROP INDEX `published`;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasKey('#__newsfeeds', 'idx_access') && $db->tableHasField('#__newsfeeds', 'access'))
		{
			$query = "ALTER TABLE `#__newsfeeds` ADD KEY `idx_access` (`access`);";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasKey('#__newsfeeds', 'idx_checkout') && $db->tableHasField('#__newsfeeds', 'checked_out'))
		{
			$query = "ALTER TABLE `#__newsfeeds` ADD KEY `idx_checkout` (`checked_out`);";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasKey('#__newsfeeds', 'idx_state') && $db->tableHasField('#__newsfeeds', 'published'))
		{
			$query = "ALTER TABLE `#__newsfeeds` ADD KEY `idx_state` (`published`);";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasKey('#__newsfeeds', 'idx_catid') && $db->tableHasField('#__newsfeeds', 'catid'))
		{
			$query = "ALTER TABLE `#__newsfeeds` ADD KEY `idx_catid` (`catid`);";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasKey('#__newsfeeds', 'idx_createdby') && $db->tableHasField('#__newsfeeds', 'created_by'))
		{
			$query = "ALTER TABLE `#__newsfeeds` ADD KEY `idx_createdby` (`created_by`);";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasKey('#__newsfeeds', 'idx_language') && $db->tableHasField('#__newsfeeds', 'language'))
		{
			$query = "ALTER TABLE `#__newsfeeds` ADD KEY `idx_language` (`language`);";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasKey('#__newsfeeds', 'idx_xreference') && $db->tableHasField('#__newsfeeds', 'xreference'))
		{
			$query = "ALTER TABLE `#__newsfeeds` ADD KEY `idx_xreference` (`xreference`);";
			$db->setQuery($query);
			$db->query();
		}
	}
}