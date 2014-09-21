<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for content
 **/
class Migration20130924000001ComContent extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query  = "";
		$query .= "ALTER TABLE `#__content_frontpage` ENGINE = MYISAM ;\n";
		$query .= "ALTER TABLE `#__content_rating` ENGINE = MYISAM;";
		$this->db->setQuery($query);
		$this->db->query();

		if (!$this->db->tableHasField('#__content', 'asset_id') && $this->db->tableHasField('#__content', 'id'))
		{
			$query = "ALTER TABLE `#__content` ADD COLUMN `asset_id` INTEGER UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the #_assets table.' AFTER `id`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if (!$this->db->tableHasField('#__content', 'featured') && $this->db->tableHasField('#__content', 'metadata'))
		{
			$query = "ALTER TABLE `#__content` ADD COLUMN `featured` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Set if article is featured.' AFTER `metadata`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if (!$this->db->tableHasKey('#__content', 'idx_featured_catid') && $this->db->tableHasField('#__content', 'featured') && $this->db->tableHasField('#__content', 'catid'))
		{
			$query = "ALTER TABLE `#__content` ADD INDEX idx_featured_catid(`featured`, `catid`);";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if (!$this->db->tableHasField('#__content', 'language') && $this->db->tableHasField('#__content', 'featured'))
		{
			$query = "ALTER TABLE `#__content` ADD COLUMN `language` CHAR(7) NOT NULL COMMENT 'The language code for the article.' AFTER `featured`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if (!$this->db->tableHasField('#__content', 'xreference') && $this->db->tableHasField('#__content', 'language'))
		{
			$query = "ALTER TABLE `#__content` ADD COLUMN `xreference` VARCHAR(50) NOT NULL COMMENT 'A reference to enable linkages to external data sets.' AFTER `language`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if (!$this->db->tableHasKey('#__content', 'idx_language') && $this->db->tableHasField('#__content', 'language'))
		{
			$query = "ALTER TABLE `#__content` ADD INDEX idx_language(`language`);";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if (!$this->db->tableHasKey('#__content', 'idx_xreference') && $this->db->tableHasField('#__content', 'xreference'))
		{
			$query = "ALTER TABLE `#__content` ADD INDEX idx_xreference(`xreference`);";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasField('#__content', 'attribs'))
		{
			$query = "ALTER TABLE `jos_content` CHANGE `attribs` `attribs` VARCHAR( 5120 ) NOT NULL;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasField('#__content', 'id'))
		{
			$query = "ALTER TABLE `#__content` CHANGE COLUMN `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasField('#__content', 'alias'))
		{
			$query = "ALTER TABLE `#__content` CHANGE COLUMN `alias` `alias` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NOT NULL DEFAULT '';";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasField('#__content', 'title_alias'))
		{
			$query = "ALTER TABLE `#__content` CHANGE COLUMN `title_alias` `title_alias` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NOT NULL DEFAULT '' COMMENT 'Deprecated in Joomla! 3.0';";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasField('#__content', 'sectionid'))
		{
			$query = "ALTER TABLE `#__content` CHANGE COLUMN `sectionid` `sectionid` INT(10) UNSIGNED NOT NULL DEFAULT '0';";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasField('#__content', 'mask'))
		{
			$query = "ALTER TABLE `#__content` CHANGE COLUMN `mask` `mask` INT(10) UNSIGNED NOT NULL DEFAULT '0';";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasField('#__content', 'catid'))
		{
			$query = "ALTER TABLE `#__content` CHANGE COLUMN `catid` `catid` INT(10) UNSIGNED NOT NULL DEFAULT '0';";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasField('#__content', 'created_by'))
		{
			$query = "ALTER TABLE `#__content` CHANGE COLUMN `created_by` `created_by` INT(10) UNSIGNED NOT NULL DEFAULT '0';";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasField('#__content', 'modified_by'))
		{
			$query = "ALTER TABLE `#__content` CHANGE COLUMN `modified_by` `modified_by` INT(10) UNSIGNED NOT NULL DEFAULT '0';";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasField('#__content', 'checked_out'))
		{
			$query = "ALTER TABLE `#__content` CHANGE COLUMN `checked_out` `checked_out` INT(10) UNSIGNED NOT NULL DEFAULT '0';";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasField('#__content', 'version'))
		{
			$query = "ALTER TABLE `#__content` CHANGE COLUMN `version` `version` INT(10) UNSIGNED NOT NULL DEFAULT '1';";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasField('#__content', 'parentid'))
		{
			$query = "ALTER TABLE `#__content` CHANGE COLUMN `parentid` `parentid` INT(10) UNSIGNED NOT NULL DEFAULT '0';";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasField('#__content', 'access'))
		{
			$query = "ALTER TABLE `#__content` CHANGE COLUMN `access` `access` INT(10) UNSIGNED NOT NULL DEFAULT '0';";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasField('#__content', 'hits'))
		{
			$query = "ALTER TABLE `#__content` CHANGE COLUMN `hits` `hits` INT(10) UNSIGNED NOT NULL DEFAULT '0';";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasKey('#__content', 'idx_section'))
		{
			$query = "ALTER TABLE `#__content` DROP INDEX `idx_section`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasField('#__content_rating', 'rating_sum'))
		{
			$query = "ALTER TABLE `#__content_rating` CHANGE COLUMN `rating_sum` `rating_sum` INT(10) UNSIGNED NOT NULL DEFAULT '0';";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasField('#__content_rating', 'rating_count'))
		{
			$query = "ALTER TABLE `#__content_rating` CHANGE COLUMN `rating_count` `rating_count` INT(10) UNSIGNED NOT NULL DEFAULT '0';";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// Update "uncategoriesed" cat_id from 0
		$query = "SELECT `id` FROM `#__categories` WHERE extension = 'com_content' AND `alias` = 'uncategorised';";
		$this->db->setQuery($query);
		$id = $this->db->loadResult();

		if (is_numeric($id))
		{
			$query = "UPDATE `#__content` set `catid` = '{$id}' WHERE `catid` = '0';";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// Convert params to json
		$query = "SELECT `id`, `attribs` FROM `#__content` WHERE `attribs` IS NOT NULL OR `attribs` != '';";
		$this->db->setQuery($query);
		$results = $this->db->loadObjectList();

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

				$query = "UPDATE `#__content` SET `attribs` = " . $this->db->Quote(json_encode($array)) . " WHERE `id` = {$r->id};";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
