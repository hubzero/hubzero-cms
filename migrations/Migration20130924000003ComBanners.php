<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for joomla banner tables
 **/
class Migration20130924000003ComBanners extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		if ($db->tableExists('#__banner') && !$db->tableExists('#__banners'))
		{
			$query = "ALTER TABLE `#__banner` RENAME TO `#__banners`;";
			$query .= "ALTER TABLE `#__banners` ENGINE = InnoDB;";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__banners', 'bid') && !$db->tableHasField('#__banners', 'id'))
		{
			$query = "ALTER TABLE `#__banners` CHANGE COLUMN `bid` `id` INTEGER NOT NULL auto_increment;";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__banners', 'alias'))
		{
			$query = "ALTER TABLE `#__banners` CHANGE COLUMN `alias` `alias` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NOT NULL DEFAULT '';";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__banners', 'checked_out'))
		{
			$query = "ALTER TABLE `#__banners` CHANGE COLUMN `checked_out` `checked_out` INT(10) UNSIGNED NOT NULL DEFAULT '0';";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__banners', 'type'))
		{
			$query = "ALTER TABLE `#__banners` MODIFY COLUMN `type` INTEGER NOT NULL DEFAULT '0';";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__banners', 'showBanner') && !$db->tableHasField('#__banners', 'state'))
		{
			$query = "ALTER TABLE `#__banners` CHANGE COLUMN `showBanner` `state` TINYINT(3) NOT NULL DEFAULT '0';";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__banners', 'tags') && !$db->tableHasField('#__banners', 'metakey'))
		{
			$query = "ALTER TABLE `#__banners` CHANGE COLUMN `tags` `metakey` TEXT NOT NULL AFTER `state`;";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__banners', 'date') && !$db->tableHasField('#__banners', 'created'))
		{
			$query = "ALTER TABLE `#__banners` CHANGE COLUMN `date` `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `params`;";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__banners', 'editor'))
		{
			$query = "ALTER TABLE `#__banners` DROP COLUMN `editor`;";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__banners', 'catid'))
		{
			$query = "ALTER TABLE `#__banners` MODIFY COLUMN `catid` INTEGER UNSIGNED NOT NULL DEFAULT 0 AFTER `state`;";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__banners', 'description') && $db->tableHasField('#__banners', 'catid'))
		{
			$query = "ALTER TABLE `#__banners` MODIFY COLUMN `description` TEXT NOT NULL AFTER `catid`;";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__banners', 'custombannercode') && $db->tableHasField('#__banners', 'description'))
		{
			$query = "ALTER TABLE `#__banners` CHANGE COLUMN `custombannercode` `custombannercode` VARCHAR(2048) NOT NULL  AFTER `description`;";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__banners', 'sticky') && $db->tableHasField('#__banners', 'description'))
		{
			$query = "ALTER TABLE `#__banners` MODIFY COLUMN `sticky` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `description`;";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__banners', 'custombannercode') && $db->tableHasField('#__banners', 'description'))
		{
			$query = "ALTER TABLE `#__banners` CHANGE COLUMN `custombannercode` `custombannercode` VARCHAR(2048) NOT NULL  AFTER `description`;";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__banners', 'ordering') && $db->tableHasField('#__banners', 'sticky'))
		{
			$query = "ALTER TABLE `#__banners` MODIFY COLUMN `ordering` INTEGER NOT NULL DEFAULT 0 AFTER `sticky`;";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__banners', 'params') && $db->tableHasField('#__banners', 'metakey'))
		{
			$query = "ALTER TABLE `#__banners` MODIFY COLUMN `params` TEXT NOT NULL AFTER `metakey`;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__banners', 'own_prefix') && $db->tableHasField('#__banners', 'params'))
		{
			$query = "ALTER TABLE `#__banners` ADD COLUMN `own_prefix` TINYINT(1) NOT NULL DEFAULT '0' AFTER `params`;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__banners', 'metakey_prefix') && $db->tableHasField('#__banners', 'own_prefix'))
		{
			$query = "ALTER TABLE `#__banners` ADD COLUMN `metakey_prefix` VARCHAR(255) NOT NULL DEFAULT '' AFTER `own_prefix`;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__banners', 'purchase_type') && $db->tableHasField('#__banners', 'metakey_prefix'))
		{
			$query = "ALTER TABLE `#__banners` ADD COLUMN `purchase_type` TINYINT NOT NULL DEFAULT '-1' AFTER `metakey_prefix`;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__banners', 'track_clicks') && $db->tableHasField('#__banners', 'purchase_type'))
		{
			$query = "ALTER TABLE `#__banners` ADD COLUMN `track_clicks` TINYINT NOT NULL DEFAULT '-1' AFTER `purchase_type`;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__banners', 'track_impressions') && $db->tableHasField('#__banners', 'track_clicks'))
		{
			$query = "ALTER TABLE `#__banners` ADD COLUMN `track_impressions` TINYINT NOT NULL DEFAULT '-1' AFTER `track_clicks`;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__banners', 'reset') && $db->tableHasField('#__banners', 'publish_down'))
		{
			$query = "ALTER TABLE `#__banners` ADD COLUMN `reset` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `publish_down`;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__banners', 'language') && $db->tableHasField('#__banners', 'created'))
		{
			$query = "ALTER TABLE `#__banners` ADD COLUMN `language` char(7) NOT NULL DEFAULT '' AFTER `created`;";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__banners', 'type') && $db->tableHasField('#__banners', 'custombannercode'))
		{
			$query = "UPDATE `#__banners` SET `type`=1 WHERE TRIM(`custombannercode`)!='';";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__banners', 'imageurl'))
		{
			$query = "ALTER TABLE `#__banners` DROP COLUMN `imageurl`;";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasKey('#__banners', 'viewbanner'))
		{
			$query = "ALTER TABLE `#__banners` DROP INDEX `viewbanner`;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasKey('#__banners', 'idx_own_prefix'))
		{
			$query = "ALTER TABLE `#__banners` ADD INDEX `idx_own_prefix` (`own_prefix`);";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasKey('#__banners', 'idx_metakey_prefix'))
		{
			$query = "ALTER TABLE `#__banners` ADD INDEX `idx_metakey_prefix` (`metakey_prefix`);";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasKey('#__banners', 'idx_language'))
		{
			$query = "ALTER TABLE `#__banners` ADD INDEX `idx_language` (`language`);";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasKey('#__banners', 'idx_state'))
		{
			$query = "ALTER TABLE `#__banners` ADD INDEX `idx_state` (`state` ASC);";
			$db->setQuery($query);
			$db->query();
		}

		if ($db->tableExists('#__bannerclient') && !$db->tableExists('#__banner_clients'))
		{
			$query = "ALTER TABLE `#__bannerclient` RENAME TO `#__banner_clients`;";
			$query .= "ALTER TABLE `#__banner_clients` ENGINE = InnoDB;";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__banner_clients', 'cid') && !$db->tableHasField('#__banner_clients', 'id'))
		{
			$query = "ALTER TABLE `#__banner_clients` CHANGE COLUMN `cid` `id` INTEGER NOT NULL auto_increment;";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__banner_clients', 'checked_out'))
		{
			$query = "ALTER TABLE `#__banner_clients` CHANGE COLUMN `checked_out` `checked_out` INT(10) UNSIGNED NOT NULL DEFAULT '0';";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__banner_clients', 'checked_out_time'))
		{
			$query = "ALTER TABLE `#__banner_clients` CHANGE COLUMN `checked_out_time` `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00';";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__banner_clients', 'editor'))
		{
			$query = "ALTER TABLE `#__banner_clients` DROP COLUMN `editor`;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__banner_clients', 'state'))
		{
			$query = "ALTER TABLE `#__banner_clients` ADD COLUMN `state` TINYINT(3) NOT NULL DEFAULT '0' AFTER `extrainfo`;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__banner_clients', 'metakey'))
		{
			$query = "ALTER TABLE `#__banner_clients` ADD COLUMN `metakey` TEXT NOT NULL;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__banner_clients', 'own_prefix'))
		{
			$query = "ALTER TABLE `#__banner_clients` ADD COLUMN `own_prefix` TINYINT NOT NULL DEFAULT '0';";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__banner_clients', 'metakey_prefix'))
		{
			$query = "ALTER TABLE `#__banner_clients` ADD COLUMN `metakey_prefix` VARCHAR(255) NOT NULL DEFAULT '';";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__banner_clients', 'purchase_type'))
		{
			$query = "ALTER TABLE `#__banner_clients` ADD COLUMN `purchase_type` TINYINT NOT NULL DEFAULT '-1';";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__banner_clients', 'track_clicks'))
		{
			$query = "ALTER TABLE `#__banner_clients` ADD COLUMN `track_clicks` TINYINT NOT NULL DEFAULT '-1';";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__banner_clients', 'track_impressions'))
		{
			$query = "ALTER TABLE `#__banner_clients` ADD COLUMN `track_impressions` TINYINT NOT NULL DEFAULT '-1';";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasKey('#__banner_clients', 'idx_own_prefix'))
		{
			$query = "ALTER TABLE `#__banner_clients` ADD INDEX `idx_own_prefix` (`own_prefix`);";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasKey('#__banner_clients', 'idx_metakey_prefix'))
		{
			$query = "ALTER TABLE `#__banner_clients` ADD INDEX `idx_metakey_prefix` (`metakey_prefix`);";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__banner_clients', 'state'))
		{
			$query = "UPDATE `#__banner_clients` SET `state`=1;";
			$db->setQuery($query);
			$db->query();
		}

		if ($db->tableExists('#__bannertrack') && !$db->tableExists('#__banner_tracks'))
		{
			$query = "ALTER TABLE `#__bannertrack` RENAME TO `#__banner_tracks`;";
			$query .= "ALTER TABLE `#__banner_tracks` ENGINE = InnoDB;";

			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__banner_tracks', 'count'))
		{
			$query = "ALTER TABLE `#__banner_tracks` ADD COLUMN `count` INTEGER UNSIGNED NOT NULL DEFAULT '0';";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableExists('#__banner_tracks'))
		{
			$query = "INSERT `#__banner_tracks`
						SELECT `track_date`,`track_type`,`banner_id`,count('*') AS `count`
						FROM `#__banner_tracks`
						GROUP BY `track_date`,`track_type`,`banner_id`;";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableExists('#__banner_tracks'))
		{
			$query = "DELETE FROM `#__banner_tracks` WHERE `count`=0;";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__banner_tracks', 'track_date'))
		{
			$query = "ALTER TABLE `#__banner_tracks` CHANGE COLUMN `track_date` `track_date` DATETIME NOT NULL;";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableExists('#__banner_tracks') && !$db->tableHasKey('#__banner_tracks', 'PRIMARY'))
		{
			$query = "ALTER TABLE `#__banner_tracks` ADD PRIMARY KEY (`track_date`, `track_type`, `banner_id`);";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasKey('#__banner_tracks', 'idx_track_date'))
		{
			$query = "ALTER TABLE `#__banner_tracks` ADD INDEX `idx_track_date` (`track_date`);";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasKey('#__banner_tracks', 'idx_track_type'))
		{
			$query = "ALTER TABLE `#__banner_tracks` ADD INDEX `idx_track_type` (`track_type`);";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasKey('#__banner_tracks', 'idx_banner_id'))
		{
			$query = "ALTER TABLE `#__banner_tracks` ADD INDEX `idx_banner_id` (`banner_id`);";
			$db->setQuery($query);
			$db->query();
		}
	}
}