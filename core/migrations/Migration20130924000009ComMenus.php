<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for menu table migrations
 **/
class Migration20130924000009ComMenus extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query = "ALTER TABLE `#__menu` ENGINE = MYISAM;";
		$this->db->setQuery($query);
		$this->db->query();

		$first = false;

		if ($this->db->tableHasField('#__menu', 'pollid'))
		{
			$query = "ALTER TABLE `#__menu` DROP COLUMN `pollid`;";
			$this->db->setQuery($query);
			$this->db->query();

			$first = true;
		}
		if ($this->db->tableHasField('#__menu', 'utaccess'))
		{
			$query = "ALTER TABLE `#__menu` DROP COLUMN `utaccess`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasField('#__menu', 'menutype'))
		{
			$query = "ALTER TABLE `#__menu` MODIFY COLUMN `menutype` VARCHAR(24) NOT NULL COMMENT 'The type of menu this item belongs to. FK to #__menu_types.menutype';";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasField('#__menu', 'name') && !$this->db->tableHasField('#__menu', 'title'))
		{
			$query = "ALTER TABLE `#__menu` CHANGE COLUMN `name` `title` VARCHAR(255) NOT NULL COMMENT 'The display title of the menu item.';";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasField('#__menu', 'alias'))
		{
			$query = "ALTER TABLE `#__menu` MODIFY COLUMN `alias` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NOT NULL COMMENT 'The SEF alias of the menu item.';";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if (!$this->db->tableHasField('#__menu', 'note') && $this->db->tableHasField('#__menu', 'alias'))
		{
			$query = "ALTER TABLE `#__menu` ADD COLUMN `note` VARCHAR(255) NOT NULL DEFAULT '' AFTER `alias`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasField('#__menu', 'link'))
		{
			$query = "ALTER TABLE `#__menu` MODIFY COLUMN `link` VARCHAR(1024) NOT NULL COMMENT 'The actually link the menu item refers to.';";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasField('#__menu', 'type'))
		{
			$query = "ALTER TABLE `#__menu` MODIFY COLUMN `type` VARCHAR(16) NOT NULL COMMENT 'The type of link: Component, URL, Alias, Separator';";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasField('#__menu', 'published'))
		{
			$query = "ALTER TABLE `#__menu` MODIFY COLUMN `published` TINYINT NOT NULL DEFAULT 0 COMMENT 'The published state of the menu link.';";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasField('#__menu', 'parent') && !$this->db->tableHasField('#__menu', 'parent_id'))
		{
			$query = "ALTER TABLE `#__menu` CHANGE COLUMN `parent` `parent_id` INT(10) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'The parent menu item in the menu tree.';";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if (!$this->db->tableHasField('#__menu', 'level') && $this->db->tableHasField('#__menu', 'parent_id') && $this->db->tableHasField('#__menu', 'sublevel'))
		{
			$query = "ALTER TABLE `#__menu` CHANGE COLUMN `sublevel` `level` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'The relative level in the tree.' AFTER `parent_id`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasField('#__menu', 'componentid') && !$this->db->tableHasField('#__menu', 'component_id'))
		{
			$query = "ALTER TABLE `#__menu` CHANGE COLUMN `componentid` `component_id` INTEGER UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to #__components.id';";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasField('#__menu', 'ordering'))
		{
			$query = "ALTER TABLE `#__menu` MODIFY COLUMN `ordering` INTEGER NOT NULL DEFAULT 0 COMMENT 'The relative ordering of the menu item in the tree.';";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasField('#__menu', 'checked_out'))
		{
			$query = "ALTER TABLE `#__menu` MODIFY COLUMN `checked_out` INTEGER UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to #__users.id';";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasField('#__menu', 'checked_out_time'))
		{
			$query = "ALTER TABLE `#__menu` MODIFY COLUMN `checked_out_time` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'The time the menu item was checked out.';";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasField('#__menu', 'browserNav'))
		{
			$query = "ALTER TABLE `#__menu` MODIFY COLUMN `browserNav` TINYINT NOT NULL DEFAULT 0 COMMENT 'The click behaviour of the link.';";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasField('#__menu', 'access'))
		{
			$query = "ALTER TABLE `#__menu` CHANGE COLUMN `access` `access` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'The access level required to view the menu item.';";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasField('#__menu', 'params'))
		{
			$query = "ALTER TABLE `#__menu` CHANGE COLUMN `params` `params` TEXT NOT NULL COMMENT 'JSON encoded data for the menu item.';";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasField('#__menu', 'lft'))
		{
			$query = "ALTER TABLE `#__menu` MODIFY COLUMN `lft` INTEGER NOT NULL DEFAULT 0 COMMENT 'Nested set lft.';";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasField('#__menu', 'rgt'))
		{
			$query = "ALTER TABLE `#__menu` MODIFY COLUMN `rgt` INTEGER NOT NULL DEFAULT 0 COMMENT 'Nested set rgt.';";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasField('#__menu', 'home'))
		{
			$query = "ALTER TABLE `#__menu` MODIFY COLUMN `home` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Indicates if this menu item is the home or default page.';";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if (!$this->db->tableHasField('#__menu', 'path') && $this->db->tableHasField('#__menu', 'note'))
		{
			$query = "ALTER TABLE `#__menu` ADD COLUMN `path` VARCHAR(1024) NOT NULL COMMENT 'The computed path of the menu item based on the alias field.' AFTER `note`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if (!$this->db->tableHasField('#__menu', 'img') && $this->db->tableHasField('#__menu', 'access'))
		{
			$query = "ALTER TABLE `#__menu` ADD COLUMN `img` varchar(255) NOT NULL COMMENT 'The image of the menu item.' AFTER `access`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if (!$this->db->tableHasField('#__menu', 'template_style_id') && $this->db->tableHasField('#__menu', 'img'))
		{
			$query = "ALTER TABLE `#__menu` ADD COLUMN `template_style_id` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `img`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if (!$this->db->tableHasField('#__menu', 'language') && $this->db->tableHasField('#__menu', 'home'))
		{
			$query = "ALTER TABLE `#__menu` ADD COLUMN `language` char(7) NOT NULL DEFAULT '' AFTER `home`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if (!$this->db->tableHasField('#__menu', 'client_id') && $this->db->tableHasField('#__menu', 'language'))
		{
			$query = "ALTER TABLE `#__menu` ADD COLUMN `client_id` TINYINT(4) NOT NULL DEFAULT 0 AFTER `language`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasKey('#__menu', 'componentid'))
		{
			$query = "ALTER TABLE `#__menu` DROP INDEX `componentid`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasKey('#__menu', 'menutype'))
		{
			$query = "ALTER TABLE `#__menu` DROP INDEX `menutype`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if (!$this->db->tableHasKey('#__menu', 'idx_componentid')
				&& $this->db->tableHasField('#__menu', 'component_id')
				&& $this->db->tableHasField('#__menu', 'menutype')
				&& $this->db->tableHasField('#__menu', 'published')
				&& $this->db->tableHasField('#__menu', 'access'))
		{
			$query = "ALTER TABLE `#__menu` ADD INDEX `idx_componentid` (`component_id`,`menutype`,`published`,`access`);";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if (!$this->db->tableHasKey('#__menu', 'idx_menutype') && $this->db->tableHasField('#__menu', 'menutype'))
		{
			$query = "ALTER TABLE `#__menu` ADD INDEX `idx_menutype` (`menutype`);";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if (!$this->db->tableHasKey('#__menu', 'idx_left_right') && $this->db->tableHasField('#__menu', 'lft') && $this->db->tableHasField('#__menu', 'rgt'))
		{
			$query = "ALTER TABLE `#__menu` ADD INDEX `idx_left_right` (`lft`,`rgt`);";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if (!$this->db->tableHasKey('#__menu', 'idx_alias') && $this->db->tableHasField('#__menu', 'alias'))
		{
			$query = "ALTER TABLE `#__menu` ADD INDEX `idx_alias` (`alias`);";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if (!$this->db->tableHasKey('#__menu', 'idx_path') && $this->db->tableHasField('#__menu', 'path'))
		{
			$query = "ALTER TABLE `#__menu` ADD INDEX `idx_path` (`path`(333) ASC);";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if (!$this->db->tableHasKey('#__menu', 'idx_language') && $this->db->tableHasField('#__menu', 'language'))
		{
			$query = "ALTER TABLE `#__menu` ADD INDEX idx_language(`language`);";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if (!$this->db->tableHasKey('#__menu', 'idx_language') && $this->db->tableHasField('#__menu', 'language'))
		{
			$query = "ALTER TABLE `#__menu` ADD INDEX idx_language(`language`);";
			$this->db->setQuery($query);
			$this->db->query();
		}

		$query = "ALTER TABLE `#__menu_types` ENGINE = MYISAM;";
		$this->db->setQuery($query);
		$this->db->query();
		if ($this->db->tableHasField('#__menu_types', 'menutype'))
		{
			$query = "ALTER TABLE `#__menu_types` MODIFY COLUMN `menutype` VARCHAR(24) NOT NULL;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasField('#__menu_types', 'title'))
		{
			$query = "ALTER TABLE `#__menu_types` MODIFY COLUMN `title` VARCHAR(48) NOT NULL;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasKey('#__menu_types', 'menutype'))
		{
			$query = "ALTER TABLE `#__menu_types` DROP INDEX `menutype`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if (!$this->db->tableHasKey('#__menu_types', 'idx_menutype') && $this->db->tableHasField('#__menu_types', 'menutype'))
		{
			$query = "ALTER TABLE `#__menu_types` ADD UNIQUE INDEX `idx_menutype` (`menutype` ASC) ;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($first)
		{
			// Joomla seems to expect the root item to be 1...blah!
			// So, if id 1 is taken, we need to clear it out
			$query = "SELECT * FROM `#__menu` WHERE `id` = 1;";
			$this->db->setQuery($query);
			$result = $this->db->loadObject();

			if ($result)
			{
				$result->id = NULL;
				$this->db->insertObject('#__menu', $result);
				$id = $this->db->insertid();

				$query = "UPDATE `#__menu` SET `parent_id` = '{$id}' WHERE `parent_id` = '1';";
				$this->db->setQuery($query);
				$this->db->query();

				$query = "UPDATE `#__modules_menu` SET `menuid` = '{$id}' WHERE `menuid` = '1';";
				$this->db->setQuery($query);
				$this->db->query();

				$query = "DELETE FROM `#__menu` WHERE `id` = '1';";
				$this->db->setQuery($query);
				$this->db->query();
			}

			// Insert new root menu item
			$query  = "INSERT INTO `#__menu` (`id`, `menutype`, `title`, `alias`, `note`, `path`, `link`, `type`, `published`, `parent_id`, `level`, `component_id`, `ordering`, `checked_out`, `checked_out_time`, `browserNav`, `access`, `img`, `template_style_id`, `params`, `lft`, `rgt`, `home`, `language`, `client_id`)\n";
			$query .= "VALUES ('1', '', 'Menu_Item_Root', 'root', '', '', '', '', 1, 0, 0, 0, 0, 0, '0000-00-00 00:00:00', 0, 0, '', 0, '', 0, 0, 0, '*', 0);";
			$this->db->setQuery($query);
			$this->db->query();

			// Get the id of the new root menu item
			$query = "SELECT id FROM `#__menu` WHERE alias = 'root';";
			$this->db->setQuery($query);
			$id = $this->db->loadResult();

			// Shift the parent_id's of the existing menus to relate to the new root
			$query = "UPDATE `#__menu` SET `parent_id` = {$id} WHERE `parent_id` = 0 AND `alias` != 'root';";
			$this->db->setQuery($query);
			$this->db->query();

			// Also increment the level 1
			$query = "UPDATE `#__menu` SET `level` = `level` + 1 WHERE `alias` != 'root';";
			$this->db->setQuery($query);
			$this->db->query();

			// Build paths
			$query = "UPDATE `#__menu` SET `path` = `alias` WHERE `alias` != 'root';";
			$this->db->setQuery($query);
			$this->db->query();

			// Get max depth
			$query = "SELECT max(level) AS level FROM `#__menu`;";
			$this->db->setQuery($query);
			$maxlevel = $this->db->loadResult();

			for ($i=2; $i <= $maxlevel; $i++)
			{
				$query = "SELECT * FROM `#__menu` WHERE level >= {$i};";
				$this->db->setQuery($query);
				$results = $this->db->loadObjectList();

				if (count($results) > 0)
				{
					foreach ($results as $r)
					{
						$query = "SELECT `alias` FROM `#__menu` WHERE `id` = {$r->parent_id};";
						$this->db->setQuery($query);
						$alias = $this->db->loadResult();

						$path  = $alias . '/' . $r->path;
						$query = "UPDATE `#__menu` SET `path` = \"{$path}\" WHERE `id` = {$r->id};";
						$this->db->setQuery($query);
						$this->db->query();
					}
				}
			}

			// Add entries for components menu on backend
			$query = "SELECT * FROM `#__components` WHERE `parent` = '0' AND `iscore` = '0' AND `enabled` = '1' AND `admin_menu_link` != '' AND `admin_menu_link` IS NOT NULL;";
			$this->db->setQuery($query);
			$results = $this->db->loadObjectList();

			if (count($results) > 0)
			{
				foreach ($results as $r)
				{
					$alias = substr($r->option, 4);
					$link  = 'index.php?' . $r->admin_menu_link;
					// Insert item
					$query  = "INSERT INTO `#__menu` (`menutype`, `title`, `alias`, `path`, `link`, `type`, `published`, `parent_id`, `level`, `component_id`, `language`, `client_id`)\n";
					$query .= "VALUES ('main', '{$r->option}', '{$alias}', '{$alias}', '{$link}', 'component', 1, 1, 1, {$r->id}, '*', 1);";
					$this->db->setQuery($query);
					$this->db->query();
				}
			}

			// If we have the nested set class available, use it to rebuild lft/rgt
			if (class_exists('JTableNested') && method_exists('JTableNested', 'rebuild'))
			{
				// Use the MySQL driver for this
				$config = \JFactory::getConfig();
				$database = \JDatabase::getInstance(
					array(
						'driver'   => 'mysql',
						'host'     => $config->get('host'),
						'user'     => $config->get('user'),
						'password' => $config->get('password'),
						'database' => $config->get('db')
					)
				);

				$table = new \JTableMenu($database);
				$table->rebuild();
			}

			// Update menu params (specifically to fix menu_image)
			$query = "SELECT `id`, `params`, `link` FROM `#__menu`;";
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
						$this->db->setQuery($query);
						$art_params = json_decode($this->db->loadResult());

						foreach ($art_params as $k => $v)
						{
							if (($v !== null) && ($v !== '') && array_key_exists($k, $array))
							{
								$array[$k] = $v;
							}
						}
					}

					$query = "UPDATE `#__menu` SET `params` = " . $this->db->Quote(json_encode($array)) . " WHERE `id` = {$r->id};";
					$this->db->setQuery($query);
					$this->db->query();
				}
			}

			// Update component_id -> extension_id
			$query = "SELECT `id`, `link`, `component_id` FROM `#__menu` WHERE `component_id` != '0';";
			$this->db->setQuery($query);
			$results = $this->db->loadObjectList();

			if (count($results) > 0)
			{
				foreach ($results as $r)
				{
					preg_match('/index\.php\?option=([a-z0-9_]+)/', $r->link, $matches);

					if (isset($matches[1]) && !empty($matches[1]))
					{
						$query = "SELECT `extension_id` FROM `#__extensions` WHERE `element` = '{$matches[1]}' AND `type` = 'component' ORDER BY `client_id` ASC LIMIT 1;";
						$this->db->setQuery($query);
						$id = $this->db->loadResult();

						$id = (!is_null($id)) ? $id : '0';

						$query = "UPDATE `#__menu` SET `component_id` = '{$id}' WHERE `id` = '{$r->id}';";
						$this->db->setQuery($query);
						$this->db->query();
					}
				}
			}

			// Set language for all menu items
			$query = "UPDATE `#__menu` SET `language` = '*';";
			$this->db->setQuery($query);
			$this->db->query();

			// Fix com_user->com_users in menu items
			$query = "SELECT `extension_id` FROM `#__extensions` WHERE `element` = 'com_users';";
			$this->db->setQuery($query);
			$id = $this->db->loadResult();

			$query = "SELECT * FROM `#__menu` WHERE `menutype` = 'default' AND (`alias` = 'login' OR `alias` = 'logout' OR `alias` = 'remind' OR `alias` = 'reset');";
			$this->db->setQuery($query);
			if ($results = $this->db->loadObjectList())
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

					$query = "UPDATE `#__menu` SET `link` = " . $this->db->quote($link) . ", `component_id` = '{$id}', `params` = " . $this->db->quote(json_encode($params)) . " WHERE `id` = '{$r->id}';";
					$this->db->setQuery($query);
					$this->db->query();
				}
			}

			// Fix menu link type menu items to be alias type
			$query = "UPDATE `#__menu` SET `type` = 'alias', `link` = 'index.php?Itemid=', `params` = REPLACE(`params`, 'menu_item', 'aliasoptions') WHERE `type` = 'menulink'";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// Now we can get rid of the components table as well
		if ($this->db->tableExists('#__components'))
		{
			$query = "DROP TABLE IF EXISTS `#__components`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableHasKey('#__menu', 'idx_client_id_parent_id_alias_language')
				&& $this->db->tableHasField('#__menu', 'client_id')
				&& $this->db->tableHasField('#__menu', 'parent_id')
				&& $this->db->tableHasField('#__menu', 'alias')
				&& $this->db->tableHasField('#__menu', 'language'))
		{
			$query = "ALTER TABLE `#__menu` ADD INDEX `idx_client_id_parent_id_alias_language` (`client_id` ASC, `parent_id` ASC, `alias` ASC, `language` ASC);";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
