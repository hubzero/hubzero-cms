<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for joomla banner tables
 **/
class Migration20130924000003ComBanners extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__banner') && !$this->db->tableExists('#__banners'))
		{
			$query = "ALTER TABLE `#__banner` RENAME TO `#__banners`;";
			$query .= "ALTER TABLE `#__banners` ENGINE = MYISAM;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__banners'))
		{
			if ($this->db->tableHasField('#__banners', 'bid') && !$this->db->tableHasField('#__banners', 'id'))
			{
				$query = "ALTER TABLE `#__banners` CHANGE COLUMN `bid` `id` INTEGER NOT NULL auto_increment;";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if ($this->db->tableHasField('#__banners', 'alias'))
			{
				$query = "ALTER TABLE `#__banners` CHANGE COLUMN `alias` `alias` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NOT NULL DEFAULT '';";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if ($this->db->tableHasField('#__banners', 'checked_out'))
			{
				$query = "ALTER TABLE `#__banners` CHANGE COLUMN `checked_out` `checked_out` INT(10) UNSIGNED NOT NULL DEFAULT '0';";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if ($this->db->tableHasField('#__banners', 'type'))
			{
				$query = "ALTER TABLE `#__banners` MODIFY COLUMN `type` INTEGER NOT NULL DEFAULT '0';";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if ($this->db->tableHasField('#__banners', 'showBanner') && !$this->db->tableHasField('#__banners', 'state'))
			{
				$query = "ALTER TABLE `#__banners` CHANGE COLUMN `showBanner` `state` TINYINT(3) NOT NULL DEFAULT '0';";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if ($this->db->tableHasField('#__banners', 'tags') && !$this->db->tableHasField('#__banners', 'metakey'))
			{
				$query = "ALTER TABLE `#__banners` CHANGE COLUMN `tags` `metakey` TEXT NOT NULL AFTER `state`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if ($this->db->tableHasField('#__banners', 'date') && !$this->db->tableHasField('#__banners', 'created'))
			{
				$query = "ALTER TABLE `#__banners` CHANGE COLUMN `date` `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `params`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if ($this->db->tableHasField('#__banners', 'editor'))
			{
				$query = "ALTER TABLE `#__banners` DROP COLUMN `editor`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if ($this->db->tableHasField('#__banners', 'catid'))
			{
				$query = "ALTER TABLE `#__banners` MODIFY COLUMN `catid` INTEGER UNSIGNED NOT NULL DEFAULT 0 AFTER `state`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if ($this->db->tableHasField('#__banners', 'description') && $this->db->tableHasField('#__banners', 'catid'))
			{
				$query = "ALTER TABLE `#__banners` MODIFY COLUMN `description` TEXT NOT NULL AFTER `catid`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if ($this->db->tableHasField('#__banners', 'custombannercode') && $this->db->tableHasField('#__banners', 'description'))
			{
				$query = "ALTER TABLE `#__banners` CHANGE COLUMN `custombannercode` `custombannercode` VARCHAR(2048) NOT NULL  AFTER `description`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if ($this->db->tableHasField('#__banners', 'sticky') && $this->db->tableHasField('#__banners', 'description'))
			{
				$query = "ALTER TABLE `#__banners` MODIFY COLUMN `sticky` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `description`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if ($this->db->tableHasField('#__banners', 'custombannercode') && $this->db->tableHasField('#__banners', 'description'))
			{
				$query = "ALTER TABLE `#__banners` CHANGE COLUMN `custombannercode` `custombannercode` VARCHAR(2048) NOT NULL  AFTER `description`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if ($this->db->tableHasField('#__banners', 'ordering') && $this->db->tableHasField('#__banners', 'sticky'))
			{
				$query = "ALTER TABLE `#__banners` MODIFY COLUMN `ordering` INTEGER NOT NULL DEFAULT 0 AFTER `sticky`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if ($this->db->tableHasField('#__banners', 'params') && $this->db->tableHasField('#__banners', 'metakey'))
			{
				$query = "ALTER TABLE `#__banners` MODIFY COLUMN `params` TEXT NOT NULL AFTER `metakey`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if (!$this->db->tableHasField('#__banners', 'own_prefix') && $this->db->tableHasField('#__banners', 'params'))
			{
				$query = "ALTER TABLE `#__banners` ADD COLUMN `own_prefix` TINYINT(1) NOT NULL DEFAULT '0' AFTER `params`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if (!$this->db->tableHasField('#__banners', 'metakey_prefix') && $this->db->tableHasField('#__banners', 'own_prefix'))
			{
				$query = "ALTER TABLE `#__banners` ADD COLUMN `metakey_prefix` VARCHAR(255) NOT NULL DEFAULT '' AFTER `own_prefix`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if (!$this->db->tableHasField('#__banners', 'purchase_type') && $this->db->tableHasField('#__banners', 'metakey_prefix'))
			{
				$query = "ALTER TABLE `#__banners` ADD COLUMN `purchase_type` TINYINT NOT NULL DEFAULT '-1' AFTER `metakey_prefix`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if (!$this->db->tableHasField('#__banners', 'track_clicks') && $this->db->tableHasField('#__banners', 'purchase_type'))
			{
				$query = "ALTER TABLE `#__banners` ADD COLUMN `track_clicks` TINYINT NOT NULL DEFAULT '-1' AFTER `purchase_type`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if (!$this->db->tableHasField('#__banners', 'track_impressions') && $this->db->tableHasField('#__banners', 'track_clicks'))
			{
				$query = "ALTER TABLE `#__banners` ADD COLUMN `track_impressions` TINYINT NOT NULL DEFAULT '-1' AFTER `track_clicks`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if (!$this->db->tableHasField('#__banners', 'reset') && $this->db->tableHasField('#__banners', 'publish_down'))
			{
				$query = "ALTER TABLE `#__banners` ADD COLUMN `reset` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `publish_down`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if (!$this->db->tableHasField('#__banners', 'language') && $this->db->tableHasField('#__banners', 'created'))
			{
				$query = "ALTER TABLE `#__banners` ADD COLUMN `language` char(7) NOT NULL DEFAULT '' AFTER `created`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if ($this->db->tableHasField('#__banners', 'type') && $this->db->tableHasField('#__banners', 'custombannercode'))
			{
				$query = "UPDATE `#__banners` SET `type`=1 WHERE TRIM(`custombannercode`)!='';";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if ($this->db->tableHasField('#__banners', 'imageurl'))
			{
				$query = "ALTER TABLE `#__banners` DROP COLUMN `imageurl`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if ($this->db->tableHasKey('#__banners', 'viewbanner'))
			{
				$query = "ALTER TABLE `#__banners` DROP INDEX `viewbanner`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if (!$this->db->tableHasKey('#__banners', 'idx_own_prefix'))
			{
				$query = "ALTER TABLE `#__banners` ADD INDEX `idx_own_prefix` (`own_prefix`);";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if (!$this->db->tableHasKey('#__banners', 'idx_metakey_prefix'))
			{
				$query = "ALTER TABLE `#__banners` ADD INDEX `idx_metakey_prefix` (`metakey_prefix`);";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if (!$this->db->tableHasKey('#__banners', 'idx_language'))
			{
				$query = "ALTER TABLE `#__banners` ADD INDEX `idx_language` (`language`);";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if (!$this->db->tableHasKey('#__banners', 'idx_state'))
			{
				$query = "ALTER TABLE `#__banners` ADD INDEX `idx_state` (`state` ASC);";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__bannerclient') && !$this->db->tableExists('#__banner_clients'))
		{
			$query = "ALTER TABLE `#__bannerclient` RENAME TO `#__banner_clients`;";
			$query .= "ALTER TABLE `#__banner_clients` ENGINE = MYISAM;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__banner_clients'))
		{
			if ($this->db->tableHasField('#__banner_clients', 'cid') && !$this->db->tableHasField('#__banner_clients', 'id'))
			{
				$query = "ALTER TABLE `#__banner_clients` CHANGE COLUMN `cid` `id` INTEGER NOT NULL auto_increment;";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if ($this->db->tableHasField('#__banner_clients', 'checked_out'))
			{
				$query = "ALTER TABLE `#__banner_clients` CHANGE COLUMN `checked_out` `checked_out` INT(10) UNSIGNED NOT NULL DEFAULT '0';";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if ($this->db->tableHasField('#__banner_clients', 'checked_out_time'))
			{
				$query = "ALTER TABLE `#__banner_clients` CHANGE COLUMN `checked_out_time` `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00';";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if ($this->db->tableHasField('#__banner_clients', 'editor'))
			{
				$query = "ALTER TABLE `#__banner_clients` DROP COLUMN `editor`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if (!$this->db->tableHasField('#__banner_clients', 'state'))
			{
				$query = "ALTER TABLE `#__banner_clients` ADD COLUMN `state` TINYINT(3) NOT NULL DEFAULT '0' AFTER `extrainfo`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if (!$this->db->tableHasField('#__banner_clients', 'metakey'))
			{
				$query = "ALTER TABLE `#__banner_clients` ADD COLUMN `metakey` TEXT NOT NULL;";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if (!$this->db->tableHasField('#__banner_clients', 'own_prefix'))
			{
				$query = "ALTER TABLE `#__banner_clients` ADD COLUMN `own_prefix` TINYINT NOT NULL DEFAULT '0';";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if (!$this->db->tableHasField('#__banner_clients', 'metakey_prefix'))
			{
				$query = "ALTER TABLE `#__banner_clients` ADD COLUMN `metakey_prefix` VARCHAR(255) NOT NULL DEFAULT '';";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if (!$this->db->tableHasField('#__banner_clients', 'purchase_type'))
			{
				$query = "ALTER TABLE `#__banner_clients` ADD COLUMN `purchase_type` TINYINT NOT NULL DEFAULT '-1';";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if (!$this->db->tableHasField('#__banner_clients', 'track_clicks'))
			{
				$query = "ALTER TABLE `#__banner_clients` ADD COLUMN `track_clicks` TINYINT NOT NULL DEFAULT '-1';";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if (!$this->db->tableHasField('#__banner_clients', 'track_impressions'))
			{
				$query = "ALTER TABLE `#__banner_clients` ADD COLUMN `track_impressions` TINYINT NOT NULL DEFAULT '-1';";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if (!$this->db->tableHasKey('#__banner_clients', 'idx_own_prefix'))
			{
				$query = "ALTER TABLE `#__banner_clients` ADD INDEX `idx_own_prefix` (`own_prefix`);";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if (!$this->db->tableHasKey('#__banner_clients', 'idx_metakey_prefix'))
			{
				$query = "ALTER TABLE `#__banner_clients` ADD INDEX `idx_metakey_prefix` (`metakey_prefix`);";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if ($this->db->tableHasField('#__banner_clients', 'state'))
			{
				$query = "UPDATE `#__banner_clients` SET `state`=1;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__bannertrack') && !$this->db->tableExists('#__banner_tracks'))
		{
			$query = "ALTER TABLE `#__bannertrack` RENAME TO `#__banner_tracks`;";
			$query .= "ALTER TABLE `#__banner_tracks` ENGINE = MYISAM;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__banner_tracks'))
		{
			if (!$this->db->tableHasField('#__banner_tracks', 'count'))
			{
				$query = "ALTER TABLE `#__banner_tracks` ADD COLUMN `count` INTEGER UNSIGNED NOT NULL DEFAULT '0';";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if ($this->db->tableExists('#__banner_tracks'))
			{
				$query = "INSERT `#__banner_tracks`
							SELECT `track_date`,`track_type`,`banner_id`,count('*') AS `count`
							FROM `#__banner_tracks`
							GROUP BY `track_date`,`track_type`,`banner_id`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if ($this->db->tableExists('#__banner_tracks'))
			{
				$query = "DELETE FROM `#__banner_tracks` WHERE `count`=0;";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if ($this->db->tableHasField('#__banner_tracks', 'track_date'))
			{
				$query = "ALTER TABLE `#__banner_tracks` CHANGE COLUMN `track_date` `track_date` DATETIME NOT NULL;";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if ($this->db->tableExists('#__banner_tracks') && !$this->db->tableHasKey('#__banner_tracks', 'PRIMARY'))
			{
				$query = "ALTER TABLE `#__banner_tracks` ADD PRIMARY KEY (`track_date`, `track_type`, `banner_id`);";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if (!$this->db->tableHasKey('#__banner_tracks', 'idx_track_date'))
			{
				$query = "ALTER TABLE `#__banner_tracks` ADD INDEX `idx_track_date` (`track_date`);";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if (!$this->db->tableHasKey('#__banner_tracks', 'idx_track_type'))
			{
				$query = "ALTER TABLE `#__banner_tracks` ADD INDEX `idx_track_type` (`track_type`);";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if (!$this->db->tableHasKey('#__banner_tracks', 'idx_banner_id'))
			{
				$query = "ALTER TABLE `#__banner_tracks` ADD INDEX `idx_banner_id` (`banner_id`);";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
