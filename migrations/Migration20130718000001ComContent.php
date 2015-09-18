<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for content
 **/
class Migration20130718000001ComContent extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		$query  = "";
		// @FIXME: relying on a fulltext index here...so we can't change to InnoDB
		//$query .= "ALTER TABLE `#__content` ENGINE = InnoDB;\n";
		$query .= "ALTER TABLE `#__content_frontpage` ENGINE = InnoDB ;\n";
		$query .= "ALTER TABLE `#__content_rating` ENGINE = InnoDB;";
		$db->setQuery($query);
		$db->query();

		if (!$db->tableHasField('#__content', 'asset_id') && $db->tableHasField('#__content', 'id'))
		{
			$query = "ALTER TABLE `#__content` ADD COLUMN `asset_id` INTEGER UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the #_assets table.' AFTER `id`;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__content', 'featured') && $db->tableHasField('#__content', 'metadata'))
		{
			$query = "ALTER TABLE `#__content` ADD COLUMN `featured` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Set if article is featured.' AFTER `metadata`;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasKey('#__content', 'idx_featured_catid') && $db->tableHasField('#__content', 'featured') && $db->tableHasField('#__content', 'catid'))
		{
			$query = "ALTER TABLE `#__content` ADD INDEX idx_featured_catid(`featured`, `catid`);";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__content', 'language') && $db->tableHasField('#__content', 'featured'))
		{
			$query = "ALTER TABLE `#__content` ADD COLUMN `language` CHAR(7) NOT NULL COMMENT 'The language code for the article.' AFTER `featured`;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__content', 'xreference') && $db->tableHasField('#__content', 'language'))
		{
			$query = "ALTER TABLE `#__content` ADD COLUMN `xreference` VARCHAR(50) NOT NULL COMMENT 'A reference to enable linkages to external data sets.' AFTER `language`;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasKey('#__content', 'idx_language') && $db->tableHasField('#__content', 'language'))
		{
			$query = "ALTER TABLE `#__content` ADD INDEX idx_language(`language`);";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasKey('#__content', 'idx_xreference') && $db->tableHasField('#__content', 'xreference'))
		{
			$query = "ALTER TABLE `#__content` ADD INDEX idx_xreference(`xreference`);";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__content', 'attribs'))
		{
			$query = "ALTER TABLE `jos_content` CHANGE `attribs` `attribs` VARCHAR( 5120 ) NOT NULL;";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__content', 'id'))
		{
			$query = "ALTER TABLE `#__content` CHANGE COLUMN `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT;";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__content', 'alias'))
		{
			$query = "ALTER TABLE `#__content` CHANGE COLUMN `alias` `alias` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NOT NULL DEFAULT '';";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__content', 'title_alias'))
		{
			$query = "ALTER TABLE `#__content` CHANGE COLUMN `title_alias` `title_alias` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NOT NULL DEFAULT '';";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__content', 'sectionid'))
		{
			$query = "ALTER TABLE `#__content` CHANGE COLUMN `sectionid` `sectionid` INT(10) UNSIGNED NOT NULL DEFAULT '0';";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__content', 'mask'))
		{
			$query = "ALTER TABLE `#__content` CHANGE COLUMN `mask` `mask` INT(10) UNSIGNED NOT NULL DEFAULT '0';";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__content', 'catid'))
		{
			$query = "ALTER TABLE `#__content` CHANGE COLUMN `catid` `catid` INT(10) UNSIGNED NOT NULL DEFAULT '0';";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__content', 'created_by'))
		{
			$query = "ALTER TABLE `#__content` CHANGE COLUMN `created_by` `created_by` INT(10) UNSIGNED NOT NULL DEFAULT '0';";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__content', 'modified_by'))
		{
			$query = "ALTER TABLE `#__content` CHANGE COLUMN `modified_by` `modified_by` INT(10) UNSIGNED NOT NULL DEFAULT '0';";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__content', 'checked_out'))
		{
			$query = "ALTER TABLE `#__content` CHANGE COLUMN `checked_out` `checked_out` INT(10) UNSIGNED NOT NULL DEFAULT '0';";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__content', 'version'))
		{
			$query = "ALTER TABLE `#__content` CHANGE COLUMN `version` `version` INT(10) UNSIGNED NOT NULL DEFAULT '1';";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__content', 'parentid'))
		{
			$query = "ALTER TABLE `#__content` CHANGE COLUMN `parentid` `parentid` INT(10) UNSIGNED NOT NULL DEFAULT '0';";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__content', 'access'))
		{
			$query = "ALTER TABLE `#__content` CHANGE COLUMN `access` `access` INT(10) UNSIGNED NOT NULL DEFAULT '0';";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__content', 'hits'))
		{
			$query = "ALTER TABLE `#__content` CHANGE COLUMN `hits` `hits` INT(10) UNSIGNED NOT NULL DEFAULT '0';";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasKey('#__content', 'idx_section'))
		{
			$query = "ALTER TABLE `#__content` DROP INDEX `idx_section`;";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__content_rating', 'rating_sum'))
		{
			$query = "ALTER TABLE `#__content_rating` CHANGE COLUMN `rating_sum` `rating_sum` INT(10) UNSIGNED NOT NULL DEFAULT '0';";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__content_rating', 'rating_count'))
		{
			$query = "ALTER TABLE `#__content_rating` CHANGE COLUMN `rating_count` `rating_count` INT(10) UNSIGNED NOT NULL DEFAULT '0';";
			$db->setQuery($query);
			$db->query();
		}

		// Update "uncategoriesed" cat_id from 0
		$query = "SELECT `id` FROM `#__categories` WHERE extension = 'com_content' AND `alias` = 'uncategorised';";
		$db->setQuery($query);
		$id = $db->loadResult();

		if (is_numeric($id))
		{
			$query = "UPDATE `#__content` set `catid` = '{$id}' WHERE `catid` = '0';";
			$db->setQuery($query);
			$db->query();
		}

		// Convert params to json
		$query = "SELECT `id`, `attribs` FROM `#__content` WHERE `attribs` IS NOT NULL OR `attribs` != '';";
		$db->setQuery($query);
		$results = $db->loadObjectList();

		if (count($results) > 0)
		{
			foreach ($results as $r)
			{
				$attribs = trim($r->attribs);
				if (empty($attribs) || $attribs[0] == '{')
				{
					continue;
				}

				$array = array();
				$ar    = explode("\n", $attribs);

				foreach ($ar as $a)
				{
					$a = trim($a);
					if (empty($a))
					{
						continue;
					}

					$ar2     = explode("=", $a, 2);
					$array[$ar2[0]] = (isset($ar2[1])) ? $ar2[1] : '';
				}

				$query = "UPDATE `#__content` SET `attribs` = " . $db->Quote(json_encode($array)) . " WHERE `id` = {$r->id};";
				$db->setQuery($query);
				$db->query();
			}
		}
	}
}
