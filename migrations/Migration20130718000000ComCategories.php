<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for joomla conversion of sections to categories
 **/
class Migration20130718000000ComCategories extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		$query = "ALTER TABLE `#__categories` ENGINE = InnoDB;";
		$db->setQuery($query);
		$db->query();

		if ($db->tableHasField('#__categories', 'parent_id'))
		{
			$query = "ALTER TABLE `#__categories` CHANGE COLUMN `parent_id` `parent_id` INT(10) UNSIGNED NOT NULL DEFAULT '0';";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__categories', 'alias'))
		{
			$query = "ALTER TABLE `#__categories` CHANGE COLUMN `alias` `alias` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NOT NULL DEFAULT '';";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__categories', 'access'))
		{
			$query = "ALTER TABLE `#__categories` CHANGE COLUMN `access` `access` INT(10) UNSIGNED NOT NULL DEFAULT '0';";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__categories', 'description'))
		{
			$query = "ALTER TABLE `#__categories` MODIFY COLUMN `description` MEDIUMTEXT NOT NULL;";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__categories', 'params'))
		{
			$query = "ALTER TABLE `#__categories` MODIFY COLUMN `params` TEXT NOT NULL;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__categories', 'lft') && $db->tableHasField('#__categories', 'parent_id'))
		{
			$query = "ALTER TABLE `#__categories` ADD COLUMN `lft` INTEGER NOT NULL DEFAULT 0 COMMENT 'Nested set lft.' AFTER `parent_id`;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__categories', 'rgt') && $db->tableHasField('#__categories', 'lft'))
		{
			$query = "ALTER TABLE `#__categories` ADD COLUMN `rgt` INTEGER NOT NULL DEFAULT 0 COMMENT 'Nested set rgt.' AFTER `lft`;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__categories', 'asset_id') && $db->tableHasField('#__categories', 'id'))
		{
			$query = "ALTER TABLE `#__categories` ADD COLUMN `asset_id` INTEGER UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the jos_assets table.' AFTER `id`;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__categories', 'level') && $db->tableHasField('#__categories', 'rgt'))
		{
			$query = "ALTER TABLE `#__categories` ADD COLUMN `level` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `rgt`;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__categories', 'path') && $db->tableHasField('#__categories', 'level'))
		{
			$query = "ALTER TABLE `#__categories` ADD COLUMN `path` VARCHAR(255) NOT NULL DEFAULT '' AFTER `level`;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__categories', 'extension') && $db->tableHasField('#__categories', 'path'))
		{
			$query = "ALTER TABLE `#__categories` ADD COLUMN `extension` varchar(50) NOT NULL default '' AFTER `path`;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__categories', 'note') && $db->tableHasField('#__categories', 'alias'))
		{
			$query = "ALTER TABLE `#__categories` ADD COLUMN `note` VARCHAR(255) NOT NULL DEFAULT '' AFTER `alias`;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__categories', 'metadesc') && $db->tableHasField('#__categories', 'params'))
		{
			$query = "ALTER TABLE `#__categories` ADD COLUMN `metadesc` VARCHAR(1024) NOT NULL COMMENT 'The meta description for the page.' AFTER `params`;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__categories', 'metakey') && $db->tableHasField('#__categories', 'metadesc'))
		{
			$query = "ALTER TABLE `#__categories` ADD COLUMN `metakey` VARCHAR(1024) NOT NULL COMMENT 'The meta keywords for the page.' AFTER `metadesc`;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__categories', 'metadata') && $db->tableHasField('#__categories', 'metakey'))
		{
			$query = "ALTER TABLE `#__categories` ADD COLUMN `metadata` VARCHAR(2048) NOT NULL COMMENT 'JSON encoded metadata properties.' AFTER `metakey`;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__categories', 'created_user_id') && $db->tableHasField('#__categories', 'metadata'))
		{
			$query = "ALTER TABLE `#__categories` ADD COLUMN `created_user_id` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `metadata`;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__categories', 'created_time') && $db->tableHasField('#__categories', 'created_user_id'))
		{
			$query = "ALTER TABLE `#__categories` ADD COLUMN `created_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `created_user_id`;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__categories', 'modified_user_id') && $db->tableHasField('#__categories', 'created_time'))
		{
			$query = "ALTER TABLE `#__categories` ADD COLUMN `modified_user_id` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `created_time`;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__categories', 'modified_time') && $db->tableHasField('#__categories', 'modified_user_id'))
		{
			$query = "ALTER TABLE `#__categories` ADD COLUMN `modified_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `modified_user_id`;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__categories', 'hits') && $db->tableHasField('#__categories', 'modified_time'))
		{
			$query = "ALTER TABLE `#__categories` ADD COLUMN `hits` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `modified_time`;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__categories', 'language') && $db->tableHasField('#__categories', 'hits'))
		{
			$query = "ALTER TABLE `#__categories` ADD COLUMN `language` CHAR(7) NOT NULL AFTER `hits`;";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasKey('#__categories', 'cat_idx'))
		{
			$query = "ALTER TABLE `#__categories` DROP INDEX `cat_idx` ;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasKey('#__categories', 'cat_idx'))
		{
			$query = "ALTER TABLE `#__categories` ADD INDEX `cat_idx` (`extension` ASC, `published` ASC, `access` ASC);";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasKey('#__categories', 'idx_alias'))
		{
			$query = "ALTER TABLE `#__categories` ADD INDEX idx_alias(`alias`);";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasKey('#__categories', 'idx_path'))
		{
			$query = "ALTER TABLE `#__categories` ADD INDEX `idx_path` (`path` ASC);";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasKey('#__categories', 'idx_left_right'))
		{
			$query = "ALTER TABLE `#__categories` ADD INDEX idx_left_right(`lft`, `rgt`);";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasKey('#__categories', 'idx_language'))
		{
			$query = "ALTER TABLE `#__categories` ADD INDEX `idx_language` (`language`);";
			$db->setQuery($query);
			$db->query();
		}

		if ($db->tableHasField('#__categories', 'section'))
		{
			// @FIXME: should we fix up references in the data to com_banner(s) here?

			$query  = "UPDATE `#__categories` SET extension = section WHERE SUBSTR(section,1,3) = 'com';\n";
			$query .= "UPDATE `#__categories` SET extension = 'com_content' WHERE SUBSTR(section,1,3) != 'com';\n";
			$query .= "UPDATE `#__categories` SET parent_id = 0 WHERE SUBSTR(section,1,3)='com';";
			$query .= "UPDATE `#__categories` SET parent_id = section WHERE SUBSTR(section,1,3) !='com';";
			$query .= "UPDATE `#__categories` SET `alias` = LOWER(title) WHERE `alias` IS NULL OR `alias` = '';";
			$query .= "UPDATE `#__categories` SET level=1;";
			$db->setQuery($query);
			$db->query();

			// Insert default "uncategorised" categories (set acces to 0, because we'll increment it later with all the old categories)
			$query  = "INSERT INTO `#__categories` (parent_id,lft,rgt,level,path,extension,title,alias,note,description,published,checked_out,checked_out_time,access,params,metadesc,metakey,metadata,created_user_id,created_time,modified_user_id,modified_time,hits,language)";
			$query .= " VALUES ";
			$query .= "( 0, 0, 0, 1, 'uncategorised', 'com_content', 'Uncategorised', 'uncategorised', '', '', 1, 0, '0000-00-00 00:00:00', 0, '{\"target\":\"\",\"image\":\"\"}', '', '', '{\"page_title\":\"\",\"author\":\"\",\"robots\":\"\"}', 62, '2010-06-28 13:26:37', 0, '0000-00-00 00:00:00', 0, '*'),";
			$query .= "( 0, 0, 0, 1, 'uncategorised', 'com_banners', 'Uncategorised', 'uncategorised', '', '', 1, 0, '0000-00-00 00:00:00', 0, '{\"target\":\"\",\"image\":\"\",\"foobar\":\"\"}', '', '', '{\"page_title\":\"\",\"author\":\"\",\"robots\":\"\"}', 62, '2010-06-28 13:27:35', 0, '0000-00-00 00:00:00', 0, '*'),";
			$query .= "( 0, 0, 0, 1, 'uncategorised', 'com_contact', 'Uncategorised', 'uncategorised', '', '', 1, 0, '0000-00-00 00:00:00', 0, '{\"target\":\"\",\"image\":\"\"}', '', '', '{\"page_title\":\"\",\"author\":\"\",\"robots\":\"\"}', 62, '2010-06-28 13:27:57', 0, '0000-00-00 00:00:00', 0, '*'),";
			$query .= "( 0, 0, 0, 1, 'uncategorised', 'com_newsfeeds', 'Uncategorised', 'uncategorised', '', '', 1, 0, '0000-00-00 00:00:00', 0, '{\"target\":\"\",\"image\":\"\"}', '', '', '{\"page_title\":\"\",\"author\":\"\",\"robots\":\"\"}', 62, '2010-06-28 13:28:15', 0, '0000-00-00 00:00:00', 0, '*'),";
			$query .= "( 0, 0, 0, 1, 'uncategorised', 'com_weblinks', 'Uncategorised', 'uncategorised', '', '', 1, 0, '0000-00-00 00:00:00', 0, '{\"target\":\"\",\"image\":\"\"}', '', '', '{\"page_title\":\"\",\"author\":\"\",\"robots\":\"\"}', 62, '2010-06-28 13:28:33', 0, '0000-00-00 00:00:00', 0, '*');";
			$db->setQuery($query);
			$db->query();

			// Grab sections and insert them into categories
			$query = "SELECT * FROM `#__sections`;";
			$db->setQuery($query);
			$results = $db->loadObjectList();

			if (count($results) > 0)
			{
				foreach ($results as $r)
				{
					$query  = "INSERT INTO `#__categories` (parent_id,lft,rgt,level,path,extension,title,alias,note,description,published,checked_out,checked_out_time,access,params,metadesc,metakey,metadata,created_user_id,created_time,modified_user_id,modified_time,hits,language) VALUES ";
					$query .= "(";
					$query .= '"0",';                                # parent_id
					$query .= '"",';                                 # lft
					$query .= '"",';                                 # rgt
					$query .= '"1",';                                # level
					$query .= $db->Quote($r->alias).",";             # path
					$query .= '"com_content",';                      # extension
					$query .= $db->Quote($r->title).",";             # title
					$query .= $db->Quote($r->alias).",";             # alias
					$query .= '"",';                                 # note
					$query .= $db->Quote($r->description).",";       # description
					$query .= $db->Quote($r->published).",";         # published
					$query .= $db->Quote($r->checked_out).",";       # checked_out
					$query .= $db->Quote($r->checked_out_time).",";  # checked_out_time
					$query .= $db->Quote($r->access).",";            # access 
					$query .= $db->Quote($r->params).",";            # params
					$query .= '"",';                                 # metadesc
					$query .= '"",';                                 # metakey
					$query .= '"",';                                 # metadata
					$query .= '"",';                                 # created_user_id
					$query .= '"",';                                 # created_time
					$query .= '"",';                                 # modified_user_id,
					$query .= '"",';                                 # modified_time
					$query .= '"",';                                 # hits
					$query .= '""';                                  # language
					$query .= ");";
					$db->setQuery($query);
					$db->query();

					// Get last id
					$id = $db->insertid();

					// Set any categories that were in recently added section to have that new category id as parent_id
					$query = "UPDATE  `#__categories` SET parent_id = {$id}, level = 2 WHERE section = {$r->id};";
					$db->setQuery($query);
					$db->query();
				}
			}

			// Insert root category and set all 1st level categories to point to it
			$query  = "INSERT INTO `#__categories` (asset_id,parent_id,lft,rgt,level,path,extension,title,alias,note,description,published,checked_out,checked_out_time,access,params,metadesc,metakey,metadata,created_user_id,created_time,modified_user_id,modified_time,hits,language) VALUES ";
			$query .= "( 0, 0, 0, 0, 0, '', 'system', 'ROOT', 'root', '', '', 1, 0, '0000-00-00 00:00:00', 1, '{}', '', '', '', 0, '2009-10-18 16:07:09', 0, '0000-00-00 00:00:00', 0, '*');";
			$query .= "UPDATE  `#__categories` SET parent_id=LAST_INSERT_ID() WHERE parent_id=0 AND id !=LAST_INSERT_ID();";
			$db->setQuery($query);
			$db->query();

			// Fix up "path" field
			$query = "UPDATE `#__categories` SET `path` = alias WHERE (`path` IS NULL OR `path` = '') AND level = 1;";
			$db->setQuery($query);
			$db->query();

			$query = "SELECT * FROM `#__categories` WHERE (`path` IS NULL OR `path` = '') AND level = 2;";
			$db->setQuery($query);
			$results = $db->loadObjectList();

			if (count($results) > 0)
			{
				foreach ($results as $r)
				{
					// Get the parent item alias
					$query = "SELECT `alias` FROM `#__categories` WHERE `id` = {$r->parent_id};";
					$db->setQuery($query);
					$alias = $db->loadResult();

					// Build path var
					$path = $alias . '/' . $r->alias;

					// Save the sub-category path
					$query = "UPDATE `#__categories` SET `path` = \"{$path}\" WHERE `id` = {$r->id};";
					$db->setQuery($query);
					$db->query();
				}
			}
		}

		if ($db->tableHasField('#__categories', 'ordering'))
		{
			$query = "ALTER TABLE `#__categories` DROP COLUMN `ordering`;";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__categories', 'image'))
		{
			$query = "ALTER TABLE `#__categories` DROP COLUMN `image`;";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__categories', 'image_position'))
		{
			$query = "ALTER TABLE `#__categories` DROP COLUMN `image_position`;";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__categories', 'editor'))
		{
			$query = "ALTER TABLE `#__categories` DROP COLUMN `editor`;";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__categories', 'count'))
		{
			$query = "ALTER TABLE `#__categories` DROP COLUMN `count`;";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__categories', 'name'))
		{
			$query = "ALTER TABLE `#__categories` DROP COLUMN `name`;";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__categories', 'section'))
		{
			$query = "ALTER TABLE `#__categories` DROP COLUMN `section`;";
			$db->setQuery($query);
			$db->query();
		}
	}
}