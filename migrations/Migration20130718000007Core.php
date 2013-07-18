<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for user groups (access control list)
 **/
class Migration20130718000007Core extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		if (!$db->tableExists('#__user_usergroup_map'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__user_usergroup_map` (
						`user_id` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT ' /* comment truncated */' ,
						`group_id` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT ' /* comment truncated */' ,
						PRIMARY KEY (`user_id`, `group_id`) )
						ENGINE = InnoDB
						DEFAULT CHARACTER SET = utf8
						COLLATE = utf8_general_ci;\n";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableExists('#__usergroups'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__usergroups` (
						`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT ' /* comment truncated */' ,
						`parent_id` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT ' /* comment truncated */' ,
						`lft` INT(11) NOT NULL DEFAULT '0' COMMENT ' /* comment truncated */' ,
						`rgt` INT(11) NOT NULL DEFAULT '0' COMMENT ' /* comment truncated */' ,
						`title` VARCHAR(100) NOT NULL DEFAULT '' ,
						PRIMARY KEY (`id`) ,
						UNIQUE INDEX `idx_usergroup_parent_title_lookup` (`parent_id` ASC, `title` ASC) ,
						INDEX `idx_usergroup_title_lookup` (`title` ASC) ,
						INDEX `idx_usergroup_adjacency_lookup` (`parent_id` ASC) ,
						INDEX `idx_usergroup_nested_set_lookup` USING BTREE (`lft` ASC, `rgt` ASC) )
						ENGINE = InnoDB
						AUTO_INCREMENT = 13
						DEFAULT CHARACTER SET = utf8
						COLLATE = utf8_general_ci;\n";
			$db->setQuery($query);
			$db->query();

			// Insert default data
			$query  = "INSERT INTO `#__usergroups` (`id` ,`parent_id` ,`lft` ,`rgt` ,`title`) VALUES\n";
			$query .= "(1, 0, 1, 20, 'Public'),";
			$query .= "(2, 1, 6, 17, 'Registered'),";
			$query .= "(3, 2, 7, 14, 'Author'),";
			$query .= "(4, 3, 8, 11, 'Editor'),";
			$query .= "(5, 4, 9, 10, 'Publisher'),";
			$query .= "(6, 1, 2, 5, 'Manager'),";
			$query .= "(7, 6, 3, 4, 'Administrator'),";
			$query .= "(8, 1, 18, 19, 'Super Users');";
			$db->setQuery($query);
			$db->query();

		}
		if (!$db->tableExists('#__viewlevels'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__viewlevels` (
						`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
						`title` varchar(100) NOT NULL DEFAULT '',
						`ordering` int(11) NOT NULL DEFAULT '0',
						`rules` varchar(5120) NOT NULL COMMENT 'JSON encoded access control.',
						PRIMARY KEY (`id`),
						UNIQUE KEY `idx_assetgroup_title_lookup` (`title`)
						)   DEFAULT CHARSET=utf8;\n";
			$db->setQuery($query);
			$db->query();

			// Insert default data
			$query  = "INSERT INTO `#__viewlevels` (`id`, `title`, `ordering`, `rules`) VALUES\n";
			$query .= "(1, 'Public', 0, '[1]'),";
			$query .= "(2, 'Registered', 1, '[6,2,8]'),";
			$query .= "(3, 'Special', 2, '[6,3,8]');";
			$db->setQuery($query);
			$db->query();

			// Update access levels on a few Joomla things as needed
			$query  = "UPDATE `#__categories` SET access = access + 1;";
			$query .= "UPDATE `#__contact_details` SET access = access + 1;";
			$query .= "UPDATE `#__content` SET access = access + 1;";
			$query .= "UPDATE `#__menu` SET access = access + 1;";
			$query .= "UPDATE `#__modules` SET access = access + 1;";
			$db->setQuery($query);
			$db->query();

			// Add rows to usergroup map table for existing users
			$query  = "SELECT u.id AS user_id, g.value FROM `#__users` AS u\n";
			$query .= "LEFT JOIN `#__core_acl_aro` AS acl ON u.id = acl.value\n";
			$query .= "LEFT JOIN `#__core_acl_groups_aro_map` AS map ON acl.id = map.aro_id\n";
			$query .= "LEFT JOIN `#__core_acl_aro_groups` AS g ON map.group_id = g.id;\n";

			$db->setQuery($query);
			$results = $db->loadObjectList();

			if (count($results) > 0)
			{
				foreach ($results as $r)
				{
					// Map old names to new
					switch ($r->value)
					{
						case 'Registered':
							$group_id = 2;
							break;
						case 'Author':
							$group_id = 3;
							break;
						case 'Editor':
							$group_id = 4;
							break;
						case 'Publisher':
							$group_id = 5;
							break;
						case 'Manager':
							$group_id = 6;
							break;
						case 'Administrator':
							$group_id = 7;
							break;
						case 'Super Administrator':
							$group_id = 8;
							break;
						default:
							$group_id = 2;
							break;
					}
					$query = "INSERT INTO `#__user_usergroup_map` VALUES ({$r->user_id}, {$group_id});";
					$db->setQuery($query);
					$db->query();
				}
			}

			// Update user params (specifically to remove timezone)
			$query = "SELECT `id`, `params` FROM `#__users`;";
			$db->setQuery($query);
			$results = $db->loadObjectList();

			if (count($results) > 0)
			{
				foreach ($results as $r)
				{
					$params = trim($r->params);
					if (empty($params) || $params == '{}')
					{
						continue;
					}

					$array = array();
					$ar    = explode("\n", $params);

					foreach ($ar as $a)
					{
						$a = trim($a);
						if (empty($a))
						{
							continue;
						}

						$ar2     = explode("=", $a);
						if ($ar2[0] == 'timezone' && $ar2[1] == 0)
						{
							continue;
						}
						elseif ($ar2[0] == 'timezone' && is_numeric($ar2[1] && $ar2[1] > 0))
						{
							// @FIXME: convert to timezone abbreviation
						}
						$array[$ar2[0]] = (isset($ar2[1])) ? $ar2[1] : '';
					}

					$query = "UPDATE `#__users` SET `params` = " . $db->Quote(json_encode($array)) . " WHERE `id` = {$r->id};";
					$db->setQuery($query);
					$db->query();
				}
			}
		}

		if (!$db->tableExists('#__user_profiles'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__user_profiles` (
						`user_id` INT(11) NOT NULL ,
						`profile_key` VARCHAR(100) NOT NULL ,
						`profile_value` VARCHAR(255) NOT NULL ,
						`ordering` INT(11) NOT NULL DEFAULT '0' ,
						UNIQUE INDEX `idx_user_id_profile_key` (`user_id` ASC, `profile_key` ASC) )
						ENGINE = InnoDB
						DEFAULT CHARACTER SET = utf8
						COLLATE = utf8_general_ci
						COMMENT = '' /* comment truncated */;\n";
			$db->setQuery($query);
			$db->query();
		}

		$query = "ALTER TABLE `#__users` ENGINE = InnoDB;";
		$db->setQuery($query);
		$db->query();

		if ($db->tableHasField('#__users', 'gid'))
		{
			$query = "ALTER TABLE `#__users` DROP COLUMN `gid`;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__users', 'lastResetTime') && $db->tableHasField('#__users', 'params'))
		{
			$query = "ALTER TABLE `#__users` ADD COLUMN `lastResetTime` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `params`;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__users', 'resetCount') && $db->tableHasField('#__users', 'lastResetTime'))
		{
			$query = "ALTER TABLE `#__users` ADD COLUMN `resetCount` INT(11) NOT NULL DEFAULT '0' AFTER `lastResetTime`;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasKey('#__users', 'idx_block') && $db->tableHasField('#__users', 'block'))
		{
			$query = "ALTER TABLE `#__users` ADD INDEX `idx_block` (`block` ASC);";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasKey('#__users', 'gid_block'))
		{
			$query = "ALTER TABLE `#__users` DROP INDEX `gid_block`;";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableExists('#__core_acl_groups_aro_map'))
		{
			$query = "DROP TABLE IF EXISTS `#__core_acl_groups_aro_map` ;";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableExists('#__core_acl_aro_sections'))
		{
			$query = "DROP TABLE IF EXISTS `#__core_acl_aro_sections` ;";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableExists('#__core_acl_aro_map'))
		{
			$query = "DROP TABLE IF EXISTS `#__core_acl_aro_map` ;";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableExists('#__core_acl_aro_groups'))
		{
			$query = "DROP TABLE IF EXISTS `#__core_acl_aro_groups` ;";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableExists('#__core_acl_aro'))
		{
			$query = "DROP TABLE IF EXISTS `#__core_acl_aro` ;";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableExists('#__groups'))
		{
			$query = "DROP TABLE IF EXISTS `#__groups` ;";
			$db->setQuery($query);
			$db->query();
		}
	}
}