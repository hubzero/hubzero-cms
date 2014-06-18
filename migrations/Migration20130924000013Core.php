<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for other changes
 **/
class Migration20130924000013Core extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		// Change config offset from '0' to 'UTC'!
		// @FIXME: should we actually set this based on offset, or assume 0?
		$configuration = file_get_contents(JPATH_ROOT . DS . 'configuration.php');
		$configuration = preg_replace('/(var \$offset[\s]*=[\s]*[\'"]*)([\-0-9]+)([\'"]*)/', '$1UTC$3', $configuration);
		file_put_contents(JPATH_ROOT . DS . 'configuration.php', $configuration);

		$query = "ALTER TABLE `#__core_log_searches` ENGINE = InnoDB;\n";
		$this->db->setQuery($query);
		$this->db->query();

		if ($this->db->tableHasField('#__core_log_searches', 'hits'))
		{
			$query = "ALTER TABLE `#__core_log_searches` CHANGE COLUMN `hits` `hits` INT(10) UNSIGNED NOT NULL DEFAULT '0';";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__sections'))
		{
			$query = "DROP TABLE IF EXISTS `#__sections` ;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableHasField('#__categories', 'section'))
		{
			$query = "ALTER TABLE `#__categories` DROP COLUMN `section`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// Reset query
		$query = "";

		if (!$this->db->tableExists('#__redirect_links'))
		{
			$query .= "CREATE TABLE `#__redirect_links` (
							`id` integer unsigned NOT NULL auto_increment,
							`old_url` VARCHAR(255) NOT NULL,
							`new_url` VARCHAR(255) NOT NULL,
							`referer` varchar(150) NOT NULL,
							`comment` varchar(255) NOT NULL,
							`hits` INT(10) UNSIGNED NOT NULL DEFAULT '0',
							`published` tinyint(4) NOT NULL,
							`created_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
							`modified_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
							PRIMARY KEY  (`id`),
							UNIQUE KEY `idx_link_old` (`old_url`),
							KEY `idx_link_modifed` (`modified_date`)
						)  DEFAULT CHARSET=utf8;\n";
		}

		if (!$this->db->tableExists('#__user_notes'))
		{
			$query .= "CREATE TABLE IF NOT EXISTS `#__user_notes` (
							`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
							`user_id` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
							`catid` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
							`subject` VARCHAR(100) NOT NULL DEFAULT '' ,
							`body` TEXT NOT NULL ,
							`state` TINYINT(3) NOT NULL DEFAULT '0' ,
							`checked_out` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
							`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
							`created_user_id` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
							`created_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
							`modified_user_id` INT(10) UNSIGNED NOT NULL ,
							`modified_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
							`review_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
							`publish_up` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
							`publish_down` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
							PRIMARY KEY (`id`) ,
							INDEX `idx_user_id` (`user_id` ASC) ,
							INDEX `idx_category_id` (`catid` ASC) )
						ENGINE = InnoDB
						DEFAULT CHARACTER SET = utf8
						COLLATE = utf8_general_ci;\n";
		}

		if (!$this->db->tableExists('#__associations'))
		{
			$query .= "CREATE TABLE IF NOT EXISTS `#__associations` (
						`id` VARCHAR(50) NOT NULL COMMENT 'A reference to the associated item.',
						`context` VARCHAR(50) NOT NULL COMMENT 'The context of the associated item.',
						`key` CHAR(32) NOT NULL COMMENT 'The key for the association computed from an md5 on associated ids.',
						PRIMARY KEY `idx_context_id` (`context`, `id`),
						INDEX `idx_key` (`key`)
						) DEFAULT CHARSET=utf8;";
		}

		if (!$this->db->tableExists('#__overrider'))
		{
			$query .= "CREATE  TABLE IF NOT EXISTS `#__overrider` (
							`id` INT(10) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key' ,
							`constant` VARCHAR(255) NOT NULL ,
							`string` TEXT NOT NULL ,
							`file` VARCHAR(255) NOT NULL ,
							PRIMARY KEY (`id`) )
						ENGINE = InnoDB
						DEFAULT CHARACTER SET = utf8
						COLLATE = utf8_general_ci;";
		}

		if ($this->db->tableExists('#__core_log_items'))
		{
			$query .= "DROP TABLE `#__core_log_items`;";
		}

		if ($this->db->tableExists('#__stats_agents'))
		{
			$query .= "DROP TABLE `#__stats_agents`;";
		}

		if ($this->db->tableExists('#__migration_backlinks'))
		{
			$query .= "DROP TABLE IF EXISTS `#__migration_backlinks` ;";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__schemas'))
		{
			$query = "CREATE TABLE `#__schemas` (
							`extension_id` INT(11) NOT NULL,
							`version_id` VARCHAR(20) NOT NULL,
							PRIMARY KEY (`extension_id`, `version_id`)
						) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();

			$query = "INSERT INTO `#__schemas` (`extension_id`, `version_id`) VALUES (700, '2.5.11');";
			$this->db->setQuery($query);
			$this->db->query();
		}

		$query = "ALTER TABLE `#__session` ENGINE = InnoDB;";
		$this->db->setQuery($query);
		$this->db->query();

		if ($this->db->tableHasField('#__session', 'session_id'))
		{
			$query  = "ALTER TABLE `#__session` CHANGE COLUMN `session_id` `session_id` VARCHAR(200) NOT NULL DEFAULT '' FIRST;";
			$query .= "ALTER TABLE `#__session` DROP PRIMARY KEY , ADD PRIMARY KEY (`session_id`);";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasField('#__session', 'client_id') && $this->db->tableHasField('#__session', 'session_id'))
		{
			$query = "ALTER TABLE `#__session` MODIFY COLUMN `client_id` TINYINT UNSIGNED NOT NULL DEFAULT 0 AFTER session_id;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasField('#__session', 'guest') && $this->db->tableHasField('#__session', 'client_id'))
		{
			$query = "ALTER TABLE `#__session` CHANGE COLUMN `guest` `guest` TINYINT(4) UNSIGNED NULL DEFAULT '1'  AFTER `client_id`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasField('#__session', 'username') && $this->db->tableHasField('#__session', 'userid'))
		{
			$query = "ALTER TABLE `#__session` MODIFY COLUMN `username` VARCHAR(150) DEFAULT '' AFTER userid;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasField('#__session', 'data') && $this->db->tableHasField('#__session', 'time'))
		{
			$query = "ALTER TABLE `#__session` CHANGE COLUMN `data` `data` MEDIUMTEXT NULL DEFAULT NULL AFTER time;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasField('#__session', 'gid'))
		{
			$query = "ALTER TABLE `#__session` DROP COLUMN `gid`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__template_styles'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__template_styles` (
							`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							`template` varchar(50) NOT NULL DEFAULT '',
							`client_id` tinyint(1) unsigned NOT NULL DEFAULT '0',
							`home` CHAR(7) NOT NULL DEFAULT '0',
							`title` varchar(255) NOT NULL DEFAULT '',
							`params` TEXT NOT NULL,
							PRIMARY KEY (`id`),
							KEY `idx_template` (`template`),
							KEY `idx_home` (`home`)
						) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();

			if ($this->db->tableExists('#__templates_menu'))
			{
				$query = "INSERT INTO `#__template_styles` VALUES
					(2, 'bluestork', '1', '0', 'Bluestork - Default', '{\"useRoundedCorners\":\"1\",\"showSiteName\":\"0\"}'),
					(3, 'atomic', '0', '0', 'Atomic - Default', '{}'),
					(4, 'beez_20', 0, 0, 'Beez2 - Default', '{\"wrapperSmall\":\"53\",\"wrapperLarge\":\"72\",\"logo\":\"images\\/joomla_black.gif\",\"sitetitle\":\"Joomla!\",\"sitedescription\":\"Open Source Content Management\",\"navposition\":\"left\",\"templatecolor\":\"personal\",\"html5\":\"0\"}'),
					(5, 'hathor', '1', '0', 'Hathor - Default', '{\"showSiteName\":\"0\",\"colourChoice\":\"\",\"boldText\":\"0\"}'),
					(6, 'beez5', 0, 0, 'Beez5 - Default', '{\"wrapperSmall\":\"53\",\"wrapperLarge\":\"72\",\"logo\":\"images\\/sampledata\\/fruitshop\\/fruits.gif\",\"sitetitle\":\"Joomla!\",\"sitedescription\":\"Open Source Content Management\",\"navposition\":\"left\",\"html5\":\"0\"}');";

				$this->db->setQuery($query);
				$this->db->query();

				// Insert all templates from extensions
				$query = "SELECT * FROM `#__extensions` WHERE `type` = 'template';";
				$this->db->setQuery($query);
				$result = $this->db->loadObjectList();

				foreach ($result as $r)
				{
					$query = "SELECT * FROM `#__template_styles` WHERE `template` = '{$r->element}';";
					$this->db->setQuery($query);
					if ($this->db->loadResult())
					{
						continue;
					}

					$query = "INSERT INTO `#__template_styles` (`template`, `client_id`, `home`, `title`, `params`) VALUES ('{$r->element}', '{$r->client_id}', '0', '".ucfirst($r->element)."', '{}');";
					$this->db->setQuery($query);
					$this->db->query();
				}

				// Update current templates to have home = 1 (one for site and one for admin)
				$query = "SELECT `template`, `client_id` FROM `#__templates_menu`;";
				$this->db->setQuery($query);
				$result = $this->db->loadObjectList();

				foreach ($result as $r)
				{
					$query = "UPDATE `#__template_styles` SET `home` = '1' WHERE `template` = '{$r->template}';";
					$this->db->setQuery($query);
					$this->db->query();
				}
			}
		}

		if ($this->db->tableExists('#__templates_menu'))
		{
			$query = "DROP TABLE IF EXISTS `#__templates_menu`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		$query = "";

		if (!$this->db->tableExists('#__updates'))
		{
			$query .= "CREATE TABLE  `#__updates` (
							`update_id` int(11) NOT NULL auto_increment,
							`update_site_id` int(11) default '0',
							`extension_id` int(11) default '0',
							`categoryid` int(11) default '0',
							`name` varchar(100) default '',
							`description` TEXT NOT NULL,
							`element` varchar(100) default '',
							`type` varchar(20) default '',
							`folder` varchar(20) default '',
							`client_id` tinyint(3) default '0',
							`version` varchar(10) default '',
							`data` TEXT NOT NULL,
							`detailsurl` TEXT NOT NULL,
							`infourl` TEXT NOT NULL,
							PRIMARY KEY  (`update_id`)
						) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Available Updates';\n";
		}
		if (!$this->db->tableExists('#__update_sites'))
		{
			$query .= "CREATE TABLE  `#__update_sites` (
							`update_site_id` int(11) NOT NULL auto_increment,
							`name` varchar(100) default '',
							`type` varchar(20) default '',
							`location` TEXT NOT NULL,
							`enabled` int(11) default '0',
							`last_check_timestamp` BIGINT(20) NULL DEFAULT '0',
							PRIMARY KEY  (`update_site_id`)
						) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Update Sites';\n";

			$query .= "INSERT INTO `#__update_sites` VALUES ";
			$query .= "(1, 'Joomla Core', 'collection', 'http://update.joomla.org/core/list.xml', 1, 0),";
			$query .= "(2, 'Joomla Extension Directory', 'collection', 'http://update.joomla.org/jed/list.xml', 1, 0),";
			$query .= "(3, 'Accredited Joomla! Translations','collection','http://update.joomla.org/language/translationlist.xml', 1 ,0);";
		}
		if (!$this->db->tableExists('#__update_sites_extensions'))
		{
			$query .= "CREATE TABLE `#__update_sites_extensions` (
							`update_site_id` INT(11) NOT NULL DEFAULT '0',
							`extension_id` INT(11) NOT NULL DEFAULT '0',
							PRIMARY KEY (`update_site_id`, `extension_id`)
						) ENGINE = InnoDB CHARACTER SET utf8 COMMENT = 'Links extensions to update sites';\n";

			$query .= "INSERT INTO `#__update_sites_extensions` VALUES (1, 700), (2, 700), (3, 600);";
		}
		if (!$this->db->tableExists('#__update_categories'))
		{
			$query .= "CREATE TABLE  `#__update_categories` (
							`categoryid` int(11) NOT NULL auto_increment,
							`name` varchar(20) default '',
							`description` TEXT NOT NULL,
							`parent` int(11) default '0',
							`updatesite` int(11) default '0',
							PRIMARY KEY  (`categoryid`)
						) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Update Categories';\n";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
