<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for creating and populating new joomla extensions table
 **/
class Migration20130718000005Core extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		if (!$db->tableExists('#__extensions'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__extensions` (
							`extension_id` INT(11) NOT NULL AUTO_INCREMENT ,
							`name` VARCHAR(100) NOT NULL ,
							`type` VARCHAR(20) NOT NULL ,
							`element` VARCHAR(100) NOT NULL ,
							`folder` VARCHAR(100) NOT NULL ,
							`client_id` TINYINT(3) NOT NULL ,
							`enabled` TINYINT(3) NOT NULL DEFAULT '1' ,
							`access` INT(10) UNSIGNED NOT NULL DEFAULT '1' ,
							`protected` TINYINT(3) NOT NULL DEFAULT '0' ,
							`manifest_cache` TEXT NOT NULL ,
							`params` TEXT NOT NULL ,
							`custom_data` TEXT NOT NULL ,
							`system_data` TEXT NOT NULL ,
							`checked_out` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
							`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
							`ordering` INT(11) NULL DEFAULT '0' ,
							`state` INT(11) NULL DEFAULT '0' ,
							PRIMARY KEY (`extension_id`) ,
							INDEX `element_clientid` (`element` ASC, `client_id` ASC) ,
							INDEX `element_folder_clientid` (`element` ASC, `folder` ASC, `client_id` ASC) ,
							INDEX `extension` (`type` ASC, `element` ASC, `folder` ASC, `client_id` ASC) )
						ENGINE = InnoDB
						DEFAULT CHARACTER SET = utf8
						COLLATE = utf8_general_ci;";

			$db->setQuery($query);
			$db->query();

			// Migrate components
			$query = "SELECT * FROM `#__components` WHERE parent = 0;";
			$db->setQuery($query);
			$results = $db->loadObjectList();

			foreach ($results as $r)
			{
				$query  = "INSERT INTO `#__extensions` (`name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`)\n";
				$query .= "VALUES ('{$r->name}', 'component', '{$r->option}', '', 0, {$r->enabled}, 1, {$r->iscore}, '', '{$r->params}', '', '', 0, '0000-00-00 00:00:00', 0, 0);";
				$db->setQuery($query);
				$db->query();
			}

			// Look for any components we missed...(backend?)
			$components = array_diff(scandir(JPATH_ROOT . DS . 'administrator' . DS . 'components'), array(".", ".."));
			foreach ($components as $c)
			{
				if (!is_dir(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . $c))
				{
					continue;
				}
				$query = "SELECT `extension_id` FROM `#__extensions` WHERE `element` = '{$c}';";
				$db->setQuery($query);
				if ($db->loadResult())
				{
					continue;
				}

				$query  = "INSERT INTO `#__extensions` (`name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES\n";
				$query .= "('{$c}', 'component', '{$c}', '', 1, 1, 1, 1, '{}', '{}', '', '', 0, '0000-00-00 00:00:00', 0, 0);";
				$db->setQuery($query);
				$db->query();
			}

			// Migrate plugins
			$query = "SELECT * FROM `#__plugins`;";
			$db->setQuery($query);
			$results = $db->loadObjectList();

			foreach ($results as $r)
			{
				// Add 1 to access level
				$r->access++;

				// Build and execute query
				$query  = "INSERT INTO `#__extensions` (`name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`)\n";
				$query .= "VALUES ('{$r->name}', 'plugin', '{$r->element}', '{$r->folder}', {$r->client_id}, {$r->published}, {$r->access}, {$r->iscore}, '', '{$r->params}', '', '', {$r->checked_out}, '{$r->checked_out_time}', {$r->ordering}, 0);";
				$db->setQuery($query);
				$db->query();
			}

			// Migrate modules (site)
			$modules = array_diff(scandir(JPATH_ROOT . DS . 'modules'), array(".", ".."));
			foreach ($modules as $m)
			{
				if (!is_dir(JPATH_ROOT . DS . 'modules' . DS . $m))
				{
					continue;
				}

				$query  = "INSERT INTO `#__extensions` (`name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES\n";
				$query .= "('{$m}', 'module', '{$m}', '', 0, 1, 1, 1, '{}', '{}', '', '', 0, '0000-00-00 00:00:00', 0, 0);";
				$db->setQuery($query);
				$db->query();
			}

			// Migrate modules (admin)
			$modules = array_diff(scandir(JPATH_ROOT . DS . 'administrator' . DS . 'modules'), array(".", ".."));
			foreach ($modules as $m)
			{
				if (!is_dir(JPATH_ROOT . DS . 'administrator' . DS . 'modules' . DS . $m))
				{
					continue;
				}

				$query  = "INSERT INTO `#__extensions` (`name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES\n";
				$query .= "('{$m}', 'module', '{$m}', '', 1, 1, 1, 1, '{}', '{}', '', '', 0, '0000-00-00 00:00:00', 0, 0);";
				$db->setQuery($query);
				$db->query();
			}

			// Migrate templates
			$templates = array_diff(scandir(JPATH_ROOT . DS . 'templates'), array(".", "..", "system"));
			foreach ($templates as $t)
			{
				if (!is_dir(JPATH_ROOT . DS . 'templates' . DS . $t))
				{
					continue;
				}

				$query  = "INSERT INTO `#__extensions` (`name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES\n";
				$query .= "('".ucfirst($t)."', 'template', '{$t}', '', 0, 1, 1, 0, '{}', '{}', '', '', 0, '0000-00-00 00:00:00', 0, 0);";
				$db->setQuery($query);
				$db->query();
			}

			// Admin templates too
			$templates = array_diff(scandir(JPATH_ROOT . DS . 'administrator' . DS . 'templates'), array(".", "..", "system"));
			foreach ($templates as $t)
			{
				if (!is_dir(JPATH_ROOT . DS . 'administrator' . DS . 'templates' . DS . $t))
				{
					continue;
				}

				$query  = "INSERT INTO `#__extensions` (`name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES\n";
				$query .= "('".ucfirst($t)."', 'template', '{$t}', '', 1, 1, 1, 0, '{}', '{}', '', '', 0, '0000-00-00 00:00:00', 0, 0);";
				$db->setQuery($query);
				$db->query();
			}

			// Migrate libraries
			$query  = "INSERT INTO `#__extensions` (`name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`)\n";
			$query .= "VALUES ('PHPMailer', 'library', 'phpmailer', '', 0, 1, 1, 1, '', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),";
			$query .= "('SimplePie', 'library', 'simplepie', '', 0, 1, 1, 1, '', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),";
			$query .= "('phputf8', 'library', 'phputf8', '', 0, 1, 1, 1, '', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),";
			$query .= "('Joomla! Web Application Framework', 'library', 'joomla', '', 0, 1, 1, 1, '{\"legacy\":false,\"name\":\"Joomla! Web Application Framework\",\"type\":\"library\",\"creationDate\":\"2008\",\"author\":\"Joomla\",\"copyright\":\"Copyright (C) 2005 - 2011 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"http:\\/\\/www.joomla.org\",\"version\":\"1.6.0\",\"description\":\"The Joomla! Web Application Framework is the Core of the Joomla! Content Management System\",\"group\":\"\"}', '{}', '', '', 0, '0000-00-00 00:00:00', 0, 0);";
			$db->setQuery($query);
			$db->query();

			// Migrate languages
			$query  = "INSERT INTO `#__extensions` (`name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES\n";
			$query .= "('English (United Kingdom)', 'language', 'en-GB', '', 0, 1, 1, 1, '', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),";
			$query .= "('English (United Kingdom)', 'language', 'en-GB', '', 1, 1, 1, 1, '', '', '', '', 0, '0000-00-00 00:00:00', 0, 0);";
			$db->setQuery($query);
			$db->query();

			// Convert params to json
			// @FIXME: do we even need to do this?
			/*$query = "SELECT `extension_id`, `params` FROM `#__extensions` WHERE `params` IS NOT NULL OR `params` != '';";
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
						$array[$ar2[0]] = (isset($ar2[1])) ? $ar2[1] : '';
					}

					$query = "UPDATE `#__extensions` SET `params` = " . $db->Quote(json_encode($array)) . " WHERE `extension_id` = {$r->extension_id};";
					$db->setQuery($query);
					$db->query();
				}
			}*/

			// Delete plugins and components tables
			if ($db->tableExists('#__plugins'))
			{
				$query = "DROP TABLE IF EXISTS `#__plugins`;";
				$db->setQuery($query);
				$db->query();
			}
			if ($db->tableExists('#__components'))
			{
				$query = "DROP TABLE IF EXISTS `#__components`;";
				$db->setQuery($query);
				$db->query();
			}
		}
	}
}