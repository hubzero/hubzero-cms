<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for menu table migrations
 **/
class Migration20130718000009ComMenus extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		$query = "ALTER TABLE `#__menu` ENGINE = InnoDB;";
		$db->setQuery($query);
		$db->query();

		$first = false;

		if ($db->tableHasField('#__menu', 'pollid'))
		{
			$query = "ALTER TABLE `#__menu` DROP COLUMN `pollid`;";
			$db->setQuery($query);
			$db->query();

			$first = true;
		}
		if ($db->tableHasField('#__menu', 'utaccess'))
		{
			$query = "ALTER TABLE `#__menu` DROP COLUMN `utaccess`;";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__menu', 'menutype'))
		{
			$query = "ALTER TABLE `#__menu` MODIFY COLUMN `menutype` VARCHAR(24) NOT NULL COMMENT 'The type of menu this item belongs to. FK to #__menu_types.menutype';";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__menu', 'name') && !$db->tableHasField('#__menu', 'title'))
		{
			$query = "ALTER TABLE `#__menu` CHANGE COLUMN `name` `title` VARCHAR(255) NOT NULL COMMENT 'The display title of the menu item.';";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__menu', 'alias'))
		{
			$query = "ALTER TABLE `#__menu` MODIFY COLUMN `alias` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NOT NULL COMMENT 'The SEF alias of the menu item.';";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__menu', 'note') && $db->tableHasField('#__menu', 'alias'))
		{
			$query = "ALTER TABLE `#__menu` ADD COLUMN `note` VARCHAR(255) NOT NULL DEFAULT '' AFTER `alias`;";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__menu', 'link'))
		{
			$query = "ALTER TABLE `#__menu` MODIFY COLUMN `link` VARCHAR(1024) NOT NULL COMMENT 'The actually link the menu item refers to.';";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__menu', 'type'))
		{
			$query = "ALTER TABLE `#__menu` MODIFY COLUMN `type` VARCHAR(16) NOT NULL COMMENT 'The type of link: Component, URL, Alias, Separator';";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__menu', 'published'))
		{
			$query = "ALTER TABLE `#__menu` MODIFY COLUMN `published` TINYINT NOT NULL DEFAULT 0 COMMENT 'The published state of the menu link.';";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__menu', 'parent') && !$db->tableHasField('#__menu', 'parent_id'))
		{
			$query = "ALTER TABLE `#__menu` CHANGE COLUMN `parent` `parent_id` INT(10) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'The parent menu item in the menu tree.';";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__menu', 'level') && $db->tableHasField('#__menu', 'parent_id') && $db->tableHasField('#__menu', 'sublevel'))
		{
			$query = "ALTER TABLE `#__menu` CHANGE COLUMN `sublevel` `level` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'The relative level in the tree.' AFTER `parent_id`;";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__menu', 'componentid') && !$db->tableHasField('#__menu', 'component_id'))
		{
			$query = "ALTER TABLE `#__menu` CHANGE COLUMN `componentid` `component_id` INTEGER UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to jos_components.id';";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__menu', 'ordering'))
		{
			$query = "ALTER TABLE `#__menu` MODIFY COLUMN `ordering` INTEGER NOT NULL DEFAULT 0 COMMENT 'The relative ordering of the menu item in the tree.';";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__menu', 'checked_out'))
		{
			$query = "ALTER TABLE `#__menu` MODIFY COLUMN `checked_out` INTEGER UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to jos_users.id';";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__menu', 'checked_out_time'))
		{
			$query = "ALTER TABLE `#__menu` MODIFY COLUMN `checked_out_time` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'The time the menu item was checked out.';";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__menu', 'browserNav'))
		{
			$query = "ALTER TABLE `#__menu` MODIFY COLUMN `browserNav` TINYINT NOT NULL DEFAULT 0 COMMENT 'The click behaviour of the link.';";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__menu', 'access'))
		{
			$query = "ALTER TABLE `#__menu` CHANGE COLUMN `access` `access` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'The access level required to view the menu item.';";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__menu', 'params'))
		{
			$query = "ALTER TABLE `#__menu` CHANGE COLUMN `params` `params` TEXT NOT NULL COMMENT 'JSON encoded data for the menu item.';";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__menu', 'lft'))
		{
			$query = "ALTER TABLE `#__menu` MODIFY COLUMN `lft` INTEGER NOT NULL DEFAULT 0 COMMENT 'Nested set lft.';";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__menu', 'rgt'))
		{
			$query = "ALTER TABLE `#__menu` MODIFY COLUMN `rgt` INTEGER NOT NULL DEFAULT 0 COMMENT 'Nested set rgt.';";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__menu', 'home'))
		{
			$query = "ALTER TABLE `#__menu` MODIFY COLUMN `home` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Indicates if this menu item is the home or default page.';";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__menu', 'path') && $db->tableHasField('#__menu', 'note'))
		{
			$query = "ALTER TABLE `#__menu` ADD COLUMN `path` VARCHAR(1024) NOT NULL COMMENT 'The computed path of the menu item based on the alias field.' AFTER `note`;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__menu', 'img') && $db->tableHasField('#__menu', 'access'))
		{
			$query = "ALTER TABLE `#__menu` ADD COLUMN `img` varchar(255) NOT NULL COMMENT 'The image of the menu item.' AFTER `access`;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__menu', 'template_style_id') && $db->tableHasField('#__menu', 'img'))
		{
			$query = "ALTER TABLE `#__menu` ADD COLUMN `template_style_id` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `img`;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__menu', 'language') && $db->tableHasField('#__menu', 'home'))
		{
			$query = "ALTER TABLE `#__menu` ADD COLUMN `language` char(7) NOT NULL DEFAULT '' AFTER `home`;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__menu', 'client_id') && $db->tableHasField('#__menu', 'language'))
		{
			$query = "ALTER TABLE `#__menu` ADD COLUMN `client_id` TINYINT(4) NOT NULL DEFAULT 0 AFTER `language`;";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasKey('#__menu', 'componentid'))
		{
			$query = "ALTER TABLE `#__menu` DROP INDEX `componentid`;";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasKey('#__menu', 'menutype'))
		{
			$query = "ALTER TABLE `#__menu` DROP INDEX `menutype`;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasKey('#__menu', 'idx_componentid')
				&& $db->tableHasField('#__menu', 'component_id')
				&& $db->tableHasField('#__menu', 'menutype')
				&& $db->tableHasField('#__menu', 'published')
				&& $db->tableHasField('#__menu', 'access'))
		{
			$query = "ALTER TABLE `#__menu` ADD INDEX `idx_componentid` (`component_id`,`menutype`,`published`,`access`);";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasKey('#__menu', 'idx_menutype') && $db->tableHasField('#__menu', 'menutype'))
		{
			$query = "ALTER TABLE `#__menu` ADD INDEX `idx_menutype` (`menutype`);";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasKey('#__menu', 'idx_left_right') && $db->tableHasField('#__menu', 'lft') && $db->tableHasField('#__menu', 'rgt'))
		{
			$query = "ALTER TABLE `#__menu` ADD INDEX `idx_left_right` (`lft`,`rgt`);";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasKey('#__menu', 'idx_alias') && $db->tableHasField('#__menu', 'alias'))
		{
			$query = "ALTER TABLE `#__menu` ADD INDEX `idx_alias` (`alias`);";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasKey('#__menu', 'idx_path') && $db->tableHasField('#__menu', 'path'))
		{
			$query = "ALTER TABLE `#__menu` ADD INDEX `idx_path` (`path`(255) ASC);";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasKey('#__menu', 'idx_language') && $db->tableHasField('#__menu', 'language'))
		{
			$query = "ALTER TABLE `#__menu` ADD INDEX idx_language(`language`);";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasKey('#__menu', 'idx_language') && $db->tableHasField('#__menu', 'language'))
		{
			$query = "ALTER TABLE `#__menu` ADD INDEX idx_language(`language`);";
			$db->setQuery($query);
			$db->query();
		}

		$query = "ALTER TABLE `#__menu_types` ENGINE = InnoDB;";
		$db->setQuery($query);
		$db->query();
		if ($db->tableHasField('#__menu_types', 'menutype'))
		{
			$query = "ALTER TABLE `#__menu_types` MODIFY COLUMN `menutype` VARCHAR(24) NOT NULL;";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__menu_types', 'title'))
		{
			$query = "ALTER TABLE `#__menu_types` MODIFY COLUMN `title` VARCHAR(48) NOT NULL;";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasKey('#__menu_types', 'menutype'))
		{
			$query = "ALTER TABLE `#__menu_types` DROP INDEX `menutype`;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasKey('#__menu_types', 'idx_menutype') && $db->tableHasField('#__menu_types', 'menutype'))
		{
			$query = "ALTER TABLE `#__menu_types` ADD UNIQUE INDEX `idx_menutype` (`menutype` ASC) ;";
			$db->setQuery($query);
			$db->query();
		}

		if ($first)
		{
			// Joomla seems to expect the root item to be 1...blah!
			// So, if id 1 is taken, we need to clear it out
			$query = "SELECT * FROM `#__menu` WHERE `id` = 1;";
			$db->setQuery($query);
			$result = $db->loadObject();

			if ($result)
			{
				$result->id = NULL;
				$db->insertObject('#__menu', $result);
				$id = $db->insertid();

				$query = "UPDATE `#__menu` SET `parent_id` = '{$id}' WHERE `parent_id` = '1';";
				$db->setQuery($query);
				$db->query();

				$query = "UPDATE `#__modules_menu` SET `menuid` = '{$id}' WHERE `menuid` = '1';";
				$db->setQuery($query);
				$db->query();

				$query = "DELETE FROM `#__menu` WHERE `id` = '1';";
				$db->setQuery($query);
				$db->query();
			}

			// Insert new root menu item
			$query  = "INSERT INTO `#__menu` (`id`, `menutype`, `title`, `alias`, `note`, `path`, `link`, `type`, `published`, `parent_id`, `level`, `component_id`, `ordering`, `checked_out`, `checked_out_time`, `browserNav`, `access`, `img`, `template_style_id`, `params`, `lft`, `rgt`, `home`, `language`, `client_id`)\n";
			$query .= "VALUES ('1', '', 'Menu_Item_Root', 'root', '', '', '', '', 1, 0, 0, 0, 0, 0, '0000-00-00 00:00:00', 0, 0, '', 0, '', 0, 0, 0, '*', 0);";
			$db->setQuery($query);
			$db->query();

			// Get the id of the new root menu item
			$query = "SELECT id FROM `#__menu` WHERE alias = 'root';";
			$db->setQuery($query);
			$id = $db->loadResult();

			// Shift the parent_id's of the existing menus to relate to the new root
			$query = "UPDATE `#__menu` SET `parent_id` = {$id} WHERE `parent_id` = 0 AND `alias` != 'root';";
			$db->setQuery($query);
			$db->query();

			// Also increment the level 1
			$query = "UPDATE `#__menu` SET `level` = `level` + 1 WHERE `alias` != 'root';";
			$db->setQuery($query);
			$db->query();

			// Build paths
			$query = "UPDATE `#__menu` SET `path` = `alias` WHERE `alias` != 'root';";
			$db->setQuery($query);
			$db->query();

			// Get max depth
			$query = "SELECT max(level) AS level FROM `#__menu`;";
			$db->setQuery($query);
			$maxlevel = $db->loadResult();

			for ($i=2; $i <= $maxlevel; $i++)
			{
				$query = "SELECT * FROM `#__menu` WHERE level >= {$i};";
				$db->setQuery($query);
				$results = $db->loadObjectList();

				if (count($results) > 0)
				{
					foreach ($results as $r)
					{
						$query = "SELECT `alias` FROM `#__menu` WHERE `id` = {$r->parent_id};";
						$db->setQuery($query);
						$alias = $db->loadResult();

						$path  = $alias . '/' . $r->path;
						$query = "UPDATE `#__menu` SET `path` = \"{$path}\" WHERE `id` = {$r->id};";
						$db->setQuery($query);
						$db->query();
					}
				}
			}

			// Add entries for components menu on backend
			$query = "SELECT * FROM `#__components` WHERE `parent` = '0' AND `iscore` = '0' AND `enabled` = '1' AND `admin_menu_link` != '' AND `admin_menu_link` IS NOT NULL;";
			$db->setQuery($query);
			$results = $db->loadObjectList();

			if (count($results) > 0)
			{
				foreach ($results as $r)
				{
					$alias = substr($r->option, 4);
					$link  = 'index.php?' . $r->admin_menu_link;
					// Insert item
					$query  = "INSERT INTO `#__menu` (`menutype`, `title`, `alias`, `path`, `link`, `type`, `published`, `parent_id`, `level`, `component_id`, `language`, `client_id`)\n";
					$query .= "VALUES ('main', '{$r->option}', '{$alias}', '{$alias}', '{$link}', 'component', 1, 1, 1, {$r->id}, '*', 1);";
					$db->setQuery($query);
					$db->query();
				}
			}

			// If we have the nested set class available, use it to rebuild lft/rgt
			if (class_exists('JTableNested') && method_exists('JTableNested', 'rebuild'))
			{
				// Use the MySQL driver for this
				$config = JFactory::getConfig();
				$database = JDatabase::getInstance(
					array(
						'driver'   => 'mysql',
						'host'     => $config->getValue('host'),
						'user'     => $config->getValue('user'),
						'password' => $config->getValue('password'),
						'database' => $config->getValue('db')
					) 
				);

				$table = new JTableMenu($database);
				$table->rebuild();
			}

			// Update menu params (specifically to fix menu_image)
			$query = "SELECT `id`, `params`, `link` FROM `#__menu`;";
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

						$ar2 = explode("=", $a, 2);
						if ($ar2[0] == 'menu_image' && $ar2[1] == "-1")
						{
							$ar2[1] = "0";
						}

						$array[$ar2[0]] = (isset($ar2[1])) ? $ar2[1] : '';
					}

					// Check to see if this menu item points to an article
					preg_match('/index\.php\?option=com_content&view=article&id=([0-9]+)/', $r->link, $matches);

					// Need to merge in content params (if applicable), as menu item params now take precidence
					if (isset($matches[1]) && !empty($matches[1]))
					{
						$query = "SELECT `attribs` FROM `#__content` WHERE `id` = '{$matches[1]}';";
						$db->setQuery($query);
						$art_params = json_decode($db->loadResult());

						foreach ($art_params as $k => $v)
						{
							if (($v !== null) && ($v !== '') && array_key_exists($k, $array))
							{
								$array[$k] = $v;
							}
						}
					}

					$query = "UPDATE `#__menu` SET `params` = " . $db->Quote(json_encode($array)) . " WHERE `id` = {$r->id};";
					$db->setQuery($query);
					$db->query();
				}
			}

			// Update component_id -> extension_id
			$query = "SELECT `id`, `link`, `component_id` FROM `#__menu` WHERE `component_id` != '0';";
			$db->setQuery($query);
			$results = $db->loadObjectList();

			if (count($results) > 0)
			{
				foreach ($results as $r)
				{
					preg_match('/index\.php\?option=([a-z0-9_]+)/', $r->link, $matches);

					if (isset($matches[1]) && !empty($matches[1]))
					{
						$query = "SELECT `extension_id` FROM `#__extensions` WHERE `element` = '{$matches[1]}' AND `type` = 'component' ORDER BY `client_id` ASC LIMIT 1;";
						$db->setQuery($query);
						$id = $db->loadResult();

						$id = (!is_null($id)) ? $id : '0';

						$query = "UPDATE `#__menu` SET `component_id` = '{$id}' WHERE `id` = '{$r->id}';";
						$db->setQuery($query);
						$db->query();
					}
				}
			}

			// Set language for all menu items
			$query = "UPDATE `#__menu` SET `language` = '*';";
			$db->setQuery($query);
			$db->query();

			// Fix com_user->com_users in menu items
			$query = "SELECT `extension_id` FROM `#__extensions` WHERE `element` = 'com_users';";
			$db->setQuery($query);
			$id = $db->loadResult();

			$query = "SELECT * FROM `#__menu` WHERE `menutype` = 'default' AND (`alias` = 'login' OR `alias` = 'logout' OR `alias` = 'remind' OR `alias` = 'reset');";
			$db->setQuery($query);
			if ($results = $db->loadObjectList())
			{
				foreach ($results as $r)
				{
					$link = preg_replace('/(index\.php\?option=com_user)(&view=[a-z]+)/', '${1}s${2}', $r->link);
					$params = json_decode($r->params);

					if ($r->alias == 'login')
					{
						$params->login_redirect_url = $params->login;
						unset($params->login);
					}

					$query = "UPDATE `#__menu` SET `link` = " . $db->quote($link) . ", `component_id` = '{$id}', `params` = " . $db->quote(json_encode($params)) . " WHERE `id` = '{$r->id}';";
					$db->setQuery($query);
					$db->query();
				}
			}

			// Fix menu link type menu items to be alias type
			$query = "UPDATE `#__menu` SET `type` = 'alias', `link` = 'index.php?Itemid=', `params` = REPLACE(`params`, 'menu_item', 'aliasoptions') WHERE `type` = 'menulink'";
			$db->setQuery($query);
			$db->query();
		}

		// Now we can get rid of the components table as well
		if ($db->tableExists('#__components'))
		{
			$query = "DROP TABLE IF EXISTS `#__components`;";
			$db->setQuery($query);
			$db->query();
		}

		if (!$db->tableHasKey('#__menu', 'idx_client_id_parent_id_alias_language')
				&& $db->tableHasField('#__menu', 'client_id')
				&& $db->tableHasField('#__menu', 'parent_id')
				&& $db->tableHasField('#__menu', 'alias')
				&& $db->tableHasField('#__menu', 'language'))
		{
			$query = "ALTER TABLE `#__menu` ADD INDEX `idx_client_id_parent_id_alias_language` (`client_id` ASC, `parent_id` ASC, `alias` ASC, `language` ASC);";
			$db->setQuery($query);
			$db->query();
		}
	}
}