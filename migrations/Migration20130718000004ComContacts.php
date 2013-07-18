<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for contact details
 **/
class Migration20130718000004ComContacts extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		$query = "ALTER TABLE `#__contact_details` ENGINE = InnoDB;";
		$db->setQuery($query);
		$db->query();

		if ($db->tableHasField('#__contact_details', 'alias'))
		{
			$query = "ALTER TABLE `#__contact_details` CHANGE COLUMN `alias` `alias` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NOT NULL DEFAULT '';";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__contact_details', 'checked_out'))
		{
			$query = "ALTER TABLE `#__contact_details` CHANGE COLUMN `checked_out` `checked_out` INT(10) UNSIGNED NOT NULL DEFAULT '0';";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__contact_details', 'access'))
		{
			$query = "ALTER TABLE `#__contact_details` CHANGE COLUMN `access` `access` INT(10) UNSIGNED NOT NULL DEFAULT '0';";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__contact_details', 'sortname1'))
		{
			$query = "ALTER TABLE `#__contact_details` ADD COLUMN `sortname1` varchar(255) NOT NULL;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__contact_details', 'sortname2'))
		{
			$query = "ALTER TABLE `#__contact_details` ADD COLUMN `sortname2` varchar(255) NOT NULL;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__contact_details', 'sortname3'))
		{
			$query = "ALTER TABLE `#__contact_details` ADD COLUMN `sortname3` varchar(255) NOT NULL;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__contact_details', 'language'))
		{
			$query = "ALTER TABLE `#__contact_details` ADD COLUMN `language` char(7) NOT NULL;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__contact_details', 'created'))
		{
			$query = "ALTER TABLE `#__contact_details` ADD COLUMN  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00';";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__contact_details', 'created_by'))
		{
			$query = "ALTER TABLE `#__contact_details` ADD COLUMN   `created_by` int(10) unsigned NOT NULL DEFAULT '0';";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__contact_details', 'created_by_alias'))
		{
			$query = "ALTER TABLE `#__contact_details` ADD COLUMN   `created_by_alias` varchar(255) NOT NULL DEFAULT '';";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__contact_details', 'modified'))
		{
			$query = "ALTER TABLE `#__contact_details` ADD COLUMN   `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00';";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__contact_details', 'modified_by'))
		{
			$query = "ALTER TABLE `#__contact_details` ADD COLUMN   `modified_by` int(10) unsigned NOT NULL DEFAULT '0';";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__contact_details', 'metakey'))
		{
			$query = "ALTER TABLE `#__contact_details` ADD COLUMN   `metakey` text NOT NULL;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__contact_details', 'metadesc'))
		{
			$query = "ALTER TABLE `#__contact_details` ADD COLUMN   `metadesc` text NOT NULL;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__contact_details', 'metadata'))
		{
			$query = "ALTER TABLE `#__contact_details` ADD COLUMN   `metadata` text NOT NULL;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__contact_details', 'featured'))
		{
			$query = "ALTER TABLE `#__contact_details` ADD COLUMN   `featured` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'Set if article is featured.';";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__contact_details', 'xreference'))
		{
			$query = "ALTER TABLE `#__contact_details` ADD COLUMN   `xreference` varchar(50) NOT NULL COMMENT 'A reference to enable linkages to external data sets.';";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__contact_details', 'publish_up'))
		{
			$query = "ALTER TABLE `#__contact_details` ADD COLUMN   `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00';";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__contact_details', 'publish_down'))
		{
			$query = "ALTER TABLE `#__contact_details` ADD COLUMN   `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00';";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__contact_details', 'published'))
		{
			$query = "ALTER TABLE `#__contact_details` CHANGE `published` `published` tinyint(1) NOT NULL DEFAULT '0';";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasKey('#__contact_details', 'catid'))
		{
			$query = "ALTER TABLE `#__contact_details` DROP INDEX `catid`;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasKey('#__contact_details', 'idx_access'))
		{
			$query = "ALTER TABLE `#__contact_details` ADD  KEY `idx_access` (`access`);";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasKey('#__contact_details', 'idx_checkout'))
		{
			$query = "ALTER TABLE `#__contact_details` ADD  KEY `idx_checkout` (`checked_out`);";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasKey('#__contact_details', 'idx_state'))
		{
			$query = "ALTER TABLE `#__contact_details` ADD INDEX `idx_state` (`published` ASC);";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasKey('#__contact_details', 'idx_catid'))
		{
			$query = "ALTER TABLE `#__contact_details` ADD  KEY `idx_catid` (`catid`);";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasKey('#__contact_details', 'idx_createdby'))
		{
			$query = "ALTER TABLE `#__contact_details` ADD  KEY `idx_createdby` (`created_by`);";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasKey('#__contact_details', 'idx_featured_catid'))
		{
			$query = "ALTER TABLE `#__contact_details` ADD  KEY `idx_featured_catid` (`featured`,`catid`);";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasKey('#__contact_details', 'idx_language'))
		{
			$query = "ALTER TABLE `#__contact_details` ADD  KEY `idx_language` (`language`);";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasKey('#__contact_details', 'idx_xreference'))
		{
			$query = "ALTER TABLE `#__contact_details` ADD  KEY `idx_xreference` (`xreference`);";
			$db->setQuery($query);
			$db->query();
		}
	}
}