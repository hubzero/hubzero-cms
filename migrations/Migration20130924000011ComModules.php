<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for modules table changes
 **/
class Migration20130924000011ComModules extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		$first = false;

		$query = "ALTER TABLE `#__modules` ENGINE = InnoDB;\n";
		$query .= "ALTER TABLE `#__modules_menu` ENGINE = InnoDB;";
		$db->setQuery($query);
		$db->query();

		if ($db->tableHasField('#__modules', 'numnews'))
		{
			$query = "ALTER TABLE `#__modules` DROP `numnews`;";
			$db->setQuery($query);
			$db->query();

			$first = true;
		}
		if ($db->tableHasField('#__modules', 'control'))
		{
			$query = "ALTER TABLE `#__modules` DROP `control`;";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__modules', 'iscore'))
		{
			$query = "ALTER TABLE `#__modules` DROP `iscore`;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__modules', 'note') && $db->tableHasField('#__modules', 'title'))
		{
			$query = "ALTER TABLE `#__modules` ADD COLUMN `note` VARCHAR(255) NOT NULL DEFAULT '' AFTER `title`;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__modules', 'language') && $db->tableHasField('#__modules', 'client_id'))
		{
			$query = "ALTER TABLE `#__modules` ADD COLUMN `language` CHAR(7) NOT NULL AFTER `client_id`;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasKey('#__modules', 'idx_language') && $db->tableHasField('#__modules', 'language'))
		{
			$query = "ALTER TABLE `#__modules` ADD INDEX `idx_language` (`language`);";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__modules', 'position'))
		{
			$query = "ALTER TABLE `#__modules` CHANGE COLUMN `position` `position` VARCHAR(50) NOT NULL DEFAULT '';";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__modules', 'title'))
		{
			$query = "ALTER TABLE `#__modules` CHANGE `title` `title` varchar(100) NOT NULL DEFAULT '';";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__modules', 'params'))
		{
			$query = "ALTER TABLE `#__modules` CHANGE COLUMN `params` `params` TEXT NOT NULL;";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__modules', 'checked_out'))
		{
			$query = "ALTER TABLE `#__modules` CHANGE COLUMN `checked_out` `checked_out` INT(10) UNSIGNED NOT NULL DEFAULT '0';";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__modules', 'access'))
		{
			$query = "ALTER TABLE `#__modules` CHANGE COLUMN `access` `access` INT(10) UNSIGNED NOT NULL DEFAULT '0';";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__modules', 'publish_up') && $db->tableHasField('#__modules', 'checked_out_time'))
		{
			$query = "ALTER TABLE `#__modules` ADD COLUMN `publish_up` datetime NOT NULL default '0000-00-00 00:00:00' AFTER `checked_out_time`;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__modules', 'publish_down') && $db->tableHasField('#__modules', 'publish_up'))
		{
			$query = "ALTER TABLE `#__modules` ADD COLUMN `publish_down` datetime NOT NULL default '0000-00-00 00:00:00' AFTER `publish_up`;";
			$db->setQuery($query);
			$db->query();
		}

		if ($first)
		{
			$query = "UPDATE `#__modules` SET `module` = 'mod_menu' WHERE `module` = 'mod_mainmenu';";
			$db->setQuery($query);
			$db->query();

			// Add modules_menu entry admin modules that previously didn't need an entry
			$query = "SELECT `id` FROM `#__modules` WHERE `published` = '1' AND `client_id` = '1';";
			$db->setQuery($query);
			$results = $db->loadObjectList();

			foreach ($results as $r)
			{
				// First, make sure it isn't already there
				$query = "SELECT * FROM `#__modules_menu` WHERE `moduleid` = '{$r->id}' AND `menuid` = '0';";
				$db->setQuery($query);
				if ($ret = $db->loadObject())
				{
					continue;
				}

				$query = "INSERT INTO `#__modules_menu` VALUES ('{$r->id}', '0');";
				$db->setQuery($query);
				$db->query();
			}

			// Update menu params (specifically to fix menu_image)
			$query = "SELECT `id`, `params`, `module` FROM `#__modules`;";
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

						$array[$ar2[0]] = (isset($ar2[1])) ? $ar2[1] : '';

						if ($r->module == 'mod_breadcrumbs')
						{
							$array['showHere'] = 0;
						}
						else if ($r->module == 'mod_newsflash')
						{
							$query = "UPDATE `#__modules` SET `module` = 'mod_articles_news' WHERE `id` = {$r->id};";
							$db->setQuery($query);
							$db->query();

							// Update a few param names
							$array['item_heading'] = 'h4';
							$array['count']        = $array['items'];
							$array['ordering']     = "a.publish_up";
							$array['layout']       = "_:vertical";
							$array['cachemode']    = "itemid";
						}
					}

					$query = "UPDATE `#__modules` SET `params` = " . $db->Quote(json_encode($array)) . " WHERE `id` = {$r->id};";
					$db->setQuery($query);
					$db->query();
				}
			}
		}
	}
}