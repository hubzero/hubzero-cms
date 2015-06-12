<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for user groups (access control list)
 **/
class Migration20130924000007Core extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__user_usergroup_map'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__user_usergroup_map` (
						`user_id` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Foreign Key to #__users.id' ,
						`group_id` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Foreign Key to #__usergroups.id' ,
						PRIMARY KEY (`user_id`, `group_id`) )
						ENGINE = MYISAM
						DEFAULT CHARACTER SET = utf8
						COLLATE = utf8_general_ci;\n";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if (!$this->db->tableExists('#__usergroups'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__usergroups` (
						`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Primary Key' ,
						`parent_id` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Adjacency List Reference Id' ,
						`lft` INT(11) NOT NULL DEFAULT '0' COMMENT 'Nested set lft.' ,
						`rgt` INT(11) NOT NULL DEFAULT '0' COMMENT 'Nested set rgt.' ,
						`title` VARCHAR(100) NOT NULL DEFAULT '' ,
						PRIMARY KEY (`id`) ,
						UNIQUE INDEX `idx_usergroup_parent_title_lookup` (`parent_id` ASC, `title` ASC) ,
						INDEX `idx_usergroup_title_lookup` (`title` ASC) ,
						INDEX `idx_usergroup_adjacency_lookup` (`parent_id` ASC) ,
						INDEX `idx_usergroup_nested_set_lookup` USING BTREE (`lft` ASC, `rgt` ASC) )
						ENGINE = MYISAM
						DEFAULT CHARACTER SET = utf8
						COLLATE = utf8_general_ci;\n";
			$this->db->setQuery($query);
			$this->db->query();

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
			$this->db->setQuery($query);
			$this->db->query();

		}
		if (!$this->db->tableExists('#__viewlevels'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__viewlevels` (
						`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
						`title` varchar(100) NOT NULL DEFAULT '',
						`ordering` int(11) NOT NULL DEFAULT '0',
						`rules` varchar(5120) NOT NULL COMMENT 'JSON encoded access control.',
						PRIMARY KEY (`id`),
						UNIQUE KEY `idx_assetgroup_title_lookup` (`title`)
						)   DEFAULT CHARSET=utf8;\n";
			$this->db->setQuery($query);
			$this->db->query();

			// Insert default data
			$query  = "INSERT INTO `#__viewlevels` (`id`, `title`, `ordering`, `rules`) VALUES\n";
			$query .= "(1, 'Public', 0, '[1]'),";
			$query .= "(2, 'Registered', 1, '[6,2,8]'),";
			$query .= "(3, 'Special', 2, '[6,3,8]');";
			$this->db->setQuery($query);
			$this->db->query();

			// Update access levels on a few Joomla things as needed
			$query  = "UPDATE `#__categories` SET access = access + 1;";
			$query .= "UPDATE `#__contact_details` SET access = access + 1;";
			$query .= "UPDATE `#__content` SET access = access + 1;";
			$query .= "UPDATE `#__menu` SET access = access + 1;";
			$query .= "UPDATE `#__modules` SET access = access + 1;";
			$this->db->setQuery($query);
			$this->db->query();

			// Add rows to usergroup map table for existing users
			$query  = "SELECT id AS user_id, usertype AS value FROM `#__users`;";
			//$query  = "SELECT u.id AS user_id, g.value FROM `#__users` AS u\n";
			//$query .= "LEFT JOIN `#__core_acl_aro` AS acl ON u.id = acl.value\n";
			//$query .= "LEFT JOIN `#__core_acl_groups_aro_map` AS map ON acl.id = map.aro_id\n";
			//$query .= "LEFT JOIN `#__core_acl_aro_groups` AS g ON map.group_id = g.id;\n";

			$this->db->setQuery($query);
			$results = $this->db->loadObjectList();

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
					$this->db->setQuery($query);
					$this->db->query();
				}
			}

			// Update user params (specifically to remove timezone)
			$query = "SELECT `id`, `params` FROM `#__users`;";
			$this->db->setQuery($query);
			$results = $this->db->loadObjectList();

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

						$ar2     = explode("=", $a, 2);
						if ($ar2[0] == 'timezone' && is_numeric($ar2[1]))
						{
							$ar2[1] = "";
						}
						$array[$ar2[0]] = (isset($ar2[1])) ? $ar2[1] : '';
					}

					$query = "UPDATE `#__users` SET `params` = " . $this->db->Quote(json_encode($array)) . " WHERE `id` = {$r->id};";
					$this->db->setQuery($query);
					$this->db->query();
				}
			}
		}

		if (!$this->db->tableExists('#__user_profiles'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__user_profiles` (
						`user_id` INT(11) NOT NULL ,
						`profile_key` VARCHAR(100) NOT NULL ,
						`profile_value` VARCHAR(255) NOT NULL ,
						`ordering` INT(11) NOT NULL DEFAULT '0' ,
						UNIQUE INDEX `idx_user_id_profile_key` (`user_id` ASC, `profile_key` ASC) )
						ENGINE = MYISAM
						DEFAULT CHARACTER SET = utf8
						COLLATE = utf8_general_ci
						COMMENT = 'Simple user profile storage table';\n";
			$this->db->setQuery($query);
			$this->db->query();
		}

		$query = "ALTER TABLE `#__users` ENGINE = MYISAM;";
		$this->db->setQuery($query);
		$this->db->query();

		if ($this->db->tableHasField('#__users', 'gid'))
		{
			$query = "ALTER TABLE `#__users` DROP COLUMN `gid`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if (!$this->db->tableHasField('#__users', 'lastResetTime') && $this->db->tableHasField('#__users', 'params'))
		{
			$query = "ALTER TABLE `#__users` ADD COLUMN `lastResetTime` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `params`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if (!$this->db->tableHasField('#__users', 'resetCount') && $this->db->tableHasField('#__users', 'lastResetTime'))
		{
			$query = "ALTER TABLE `#__users` ADD COLUMN `resetCount` INT(11) NOT NULL DEFAULT '0' AFTER `lastResetTime`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if (!$this->db->tableHasKey('#__users', 'idx_block') && $this->db->tableHasField('#__users', 'block'))
		{
			$query = "ALTER TABLE `#__users` ADD INDEX `idx_block` (`block` ASC);";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasKey('#__users', 'gid_block'))
		{
			$query = "ALTER TABLE `#__users` DROP INDEX `gid_block`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableExists('#__core_acl_groups_aro_map'))
		{
			$query = "DROP TABLE IF EXISTS `#__core_acl_groups_aro_map` ;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableExists('#__core_acl_aro_sections'))
		{
			$query = "DROP TABLE IF EXISTS `#__core_acl_aro_sections` ;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableExists('#__core_acl_aro_map'))
		{
			$query = "DROP TABLE IF EXISTS `#__core_acl_aro_map` ;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableExists('#__core_acl_aro_groups'))
		{
			$query = "DROP TABLE IF EXISTS `#__core_acl_aro_groups` ;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableExists('#__core_acl_aro'))
		{
			$query = "DROP TABLE IF EXISTS `#__core_acl_aro` ;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableExists('#__groups'))
		{
			$query = "DROP TABLE IF EXISTS `#__groups` ;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
