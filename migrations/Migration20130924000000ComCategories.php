<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for joomla conversion of sections to categories
 **/
class Migration20130924000000ComCategories extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query = "ALTER TABLE `#__categories` ENGINE = MYISAM;";
		$this->db->setQuery($query);
		$this->db->query();

		if ($this->db->tableHasField('#__categories', 'parent_id'))
		{
			$query = "ALTER TABLE `#__categories` CHANGE COLUMN `parent_id` `parent_id` INT(10) UNSIGNED NOT NULL DEFAULT '0';";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasField('#__categories', 'alias'))
		{
			$query = "ALTER TABLE `#__categories` CHANGE COLUMN `alias` `alias` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NOT NULL DEFAULT '';";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasField('#__categories', 'access'))
		{
			$query = "ALTER TABLE `#__categories` CHANGE COLUMN `access` `access` INT(10) UNSIGNED NOT NULL DEFAULT '0';";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasField('#__categories', 'description'))
		{
			$query = "ALTER TABLE `#__categories` MODIFY COLUMN `description` MEDIUMTEXT NOT NULL;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasField('#__categories', 'params'))
		{
			$query = "ALTER TABLE `#__categories` MODIFY COLUMN `params` TEXT NOT NULL;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if (!$this->db->tableHasField('#__categories', 'lft') && $this->db->tableHasField('#__categories', 'parent_id'))
		{
			$query = "ALTER TABLE `#__categories` ADD COLUMN `lft` INTEGER NOT NULL DEFAULT 0 COMMENT 'Nested set lft.' AFTER `parent_id`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if (!$this->db->tableHasField('#__categories', 'rgt') && $this->db->tableHasField('#__categories', 'lft'))
		{
			$query = "ALTER TABLE `#__categories` ADD COLUMN `rgt` INTEGER NOT NULL DEFAULT 0 COMMENT 'Nested set rgt.' AFTER `lft`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if (!$this->db->tableHasField('#__categories', 'asset_id') && $this->db->tableHasField('#__categories', 'id'))
		{
			$query = "ALTER TABLE `#__categories` ADD COLUMN `asset_id` INTEGER UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the #__assets table.' AFTER `id`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if (!$this->db->tableHasField('#__categories', 'level') && $this->db->tableHasField('#__categories', 'rgt'))
		{
			$query = "ALTER TABLE `#__categories` ADD COLUMN `level` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `rgt`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if (!$this->db->tableHasField('#__categories', 'path') && $this->db->tableHasField('#__categories', 'level'))
		{
			$query = "ALTER TABLE `#__categories` ADD COLUMN `path` VARCHAR(255) NOT NULL DEFAULT '' AFTER `level`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if (!$this->db->tableHasField('#__categories', 'extension') && $this->db->tableHasField('#__categories', 'path'))
		{
			$query = "ALTER TABLE `#__categories` ADD COLUMN `extension` varchar(50) NOT NULL default '' AFTER `path`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if (!$this->db->tableHasField('#__categories', 'note') && $this->db->tableHasField('#__categories', 'alias'))
		{
			$query = "ALTER TABLE `#__categories` ADD COLUMN `note` VARCHAR(255) NOT NULL DEFAULT '' AFTER `alias`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if (!$this->db->tableHasField('#__categories', 'metadesc') && $this->db->tableHasField('#__categories', 'params'))
		{
			$query = "ALTER TABLE `#__categories` ADD COLUMN `metadesc` VARCHAR(1024) NOT NULL COMMENT 'The meta description for the page.' AFTER `params`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if (!$this->db->tableHasField('#__categories', 'metakey') && $this->db->tableHasField('#__categories', 'metadesc'))
		{
			$query = "ALTER TABLE `#__categories` ADD COLUMN `metakey` VARCHAR(1024) NOT NULL COMMENT 'The meta keywords for the page.' AFTER `metadesc`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if (!$this->db->tableHasField('#__categories', 'metadata') && $this->db->tableHasField('#__categories', 'metakey'))
		{
			$query = "ALTER TABLE `#__categories` ADD COLUMN `metadata` VARCHAR(2048) NOT NULL COMMENT 'JSON encoded metadata properties.' AFTER `metakey`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if (!$this->db->tableHasField('#__categories', 'created_user_id') && $this->db->tableHasField('#__categories', 'metadata'))
		{
			$query = "ALTER TABLE `#__categories` ADD COLUMN `created_user_id` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `metadata`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if (!$this->db->tableHasField('#__categories', 'created_time') && $this->db->tableHasField('#__categories', 'created_user_id'))
		{
			$query = "ALTER TABLE `#__categories` ADD COLUMN `created_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `created_user_id`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if (!$this->db->tableHasField('#__categories', 'modified_user_id') && $this->db->tableHasField('#__categories', 'created_time'))
		{
			$query = "ALTER TABLE `#__categories` ADD COLUMN `modified_user_id` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `created_time`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if (!$this->db->tableHasField('#__categories', 'modified_time') && $this->db->tableHasField('#__categories', 'modified_user_id'))
		{
			$query = "ALTER TABLE `#__categories` ADD COLUMN `modified_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `modified_user_id`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if (!$this->db->tableHasField('#__categories', 'hits') && $this->db->tableHasField('#__categories', 'modified_time'))
		{
			$query = "ALTER TABLE `#__categories` ADD COLUMN `hits` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `modified_time`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if (!$this->db->tableHasField('#__categories', 'language') && $this->db->tableHasField('#__categories', 'hits'))
		{
			$query = "ALTER TABLE `#__categories` ADD COLUMN `language` CHAR(7) NOT NULL AFTER `hits`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasKey('#__categories', 'cat_idx'))
		{
			$query = "ALTER TABLE `#__categories` DROP INDEX `cat_idx` ;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if (!$this->db->tableHasKey('#__categories', 'idx_extension_published_access'))
		{
			$query = "ALTER TABLE `#__categories` ADD INDEX `idx_extension_published_access` (`extension` ASC, `published` ASC, `access` ASC);";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if (!$this->db->tableHasKey('#__categories', 'idx_alias'))
		{
			$query = "ALTER TABLE `#__categories` ADD INDEX idx_alias(`alias`);";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if (!$this->db->tableHasKey('#__categories', 'idx_path'))
		{
			$query = "ALTER TABLE `#__categories` ADD INDEX `idx_path` (`path` ASC);";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if (!$this->db->tableHasKey('#__categories', 'idx_left_right'))
		{
			$query = "ALTER TABLE `#__categories` ADD INDEX idx_left_right(`lft`, `rgt`);";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if (!$this->db->tableHasKey('#__categories', 'idx_language'))
		{
			$query = "ALTER TABLE `#__categories` ADD INDEX `idx_language` (`language`);";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableHasField('#__categories', 'section'))
		{
			// @FIXME: should we fix up references in the data to com_banner(s) here?

			$query  = "UPDATE `#__categories` SET extension = section WHERE SUBSTR(section,1,3) = 'com';\n";
			$query .= "UPDATE `#__categories` SET extension = 'com_content' WHERE SUBSTR(section,1,3) != 'com';\n";
			$query .= "UPDATE `#__categories` SET parent_id = 0 WHERE SUBSTR(section,1,3)='com';";
			$query .= "UPDATE `#__categories` SET parent_id = section WHERE SUBSTR(section,1,3) !='com';";
			$query .= "UPDATE `#__categories` SET `alias` = LOWER(title) WHERE `alias` IS NULL OR `alias` = '';";
			$query .= "UPDATE `#__categories` SET level=1;";
			$this->db->setQuery($query);
			$this->db->query();

			// Insert default "uncategorised" categories (set acces to 0, because we'll increment it later with all the old categories)
			$query  = "INSERT INTO `#__categories` (parent_id,lft,rgt,level,path,extension,title,alias,note,description,published,checked_out,checked_out_time,access,params,metadesc,metakey,metadata,created_user_id,created_time,modified_user_id,modified_time,hits,language)";
			$query .= " VALUES ";
			$query .= "( 0, 0, 0, 1, 'uncategorised', 'com_content', 'Uncategorised', 'uncategorised', '', '', 1, 0, '0000-00-00 00:00:00', 0, '{\"target\":\"\",\"image\":\"\"}', '', '', '{\"page_title\":\"\",\"author\":\"\",\"robots\":\"\"}', 62, '2010-06-28 13:26:37', 0, '0000-00-00 00:00:00', 0, '*'),";
			$query .= "( 0, 0, 0, 1, 'uncategorised', 'com_banners', 'Uncategorised', 'uncategorised', '', '', 1, 0, '0000-00-00 00:00:00', 0, '{\"target\":\"\",\"image\":\"\",\"foobar\":\"\"}', '', '', '{\"page_title\":\"\",\"author\":\"\",\"robots\":\"\"}', 62, '2010-06-28 13:27:35', 0, '0000-00-00 00:00:00', 0, '*'),";
			$query .= "( 0, 0, 0, 1, 'uncategorised', 'com_contact', 'Uncategorised', 'uncategorised', '', '', 1, 0, '0000-00-00 00:00:00', 0, '{\"target\":\"\",\"image\":\"\"}', '', '', '{\"page_title\":\"\",\"author\":\"\",\"robots\":\"\"}', 62, '2010-06-28 13:27:57', 0, '0000-00-00 00:00:00', 0, '*'),";
			$query .= "( 0, 0, 0, 1, 'uncategorised', 'com_newsfeeds', 'Uncategorised', 'uncategorised', '', '', 1, 0, '0000-00-00 00:00:00', 0, '{\"target\":\"\",\"image\":\"\"}', '', '', '{\"page_title\":\"\",\"author\":\"\",\"robots\":\"\"}', 62, '2010-06-28 13:28:15', 0, '0000-00-00 00:00:00', 0, '*'),";
			$query .= "( 0, 0, 0, 1, 'uncategorised', 'com_weblinks', 'Uncategorised', 'uncategorised', '', '', 1, 0, '0000-00-00 00:00:00', 0, '{\"target\":\"\",\"image\":\"\"}', '', '', '{\"page_title\":\"\",\"author\":\"\",\"robots\":\"\"}', 62, '2010-06-28 13:28:33', 0, '0000-00-00 00:00:00', 0, '*');";
			$this->db->setQuery($query);
			$this->db->query();

			// Grab sections and insert them into categories
			$query = "SELECT * FROM `#__sections`;";
			$this->db->setQuery($query);
			$results = $this->db->loadObjectList();

			if (count($results) > 0)
			{
				foreach ($results as $r)
				{
					// Collapse duplicate section/categories down into one level
					$query = "SELECT `id` FROM `#__categories` WHERE `alias` = '{$r->alias}';";
					$this->db->setQuery($query);
					if ($this->db->loadResult())
					{
						// Set any categories that were in recently added section to have that new category id as parent_id
						$query = "UPDATE  `#__categories` SET parent_id = 0 WHERE section = {$r->id};";
						$this->db->setQuery($query);
						$this->db->query();

						continue;
					}

					$query  = "INSERT INTO `#__categories` (parent_id,lft,rgt,level,path,extension,title,alias,note,description,published,checked_out,checked_out_time,access,params,metadesc,metakey,metadata,created_user_id,created_time,modified_user_id,modified_time,hits,language) VALUES ";
					$query .= "(";
					$query .= '"0",';                                # parent_id
					$query .= '"",';                                 # lft
					$query .= '"",';                                 # rgt
					$query .= '"1",';                                # level
					$query .= $this->db->Quote($r->alias).",";             # path
					$query .= '"com_content",';                      # extension
					$query .= $this->db->Quote($r->title).",";             # title
					$query .= $this->db->Quote($r->alias).",";             # alias
					$query .= '"",';                                 # note
					$query .= $this->db->Quote($r->description).",";       # description
					$query .= $this->db->Quote($r->published).",";         # published
					$query .= $this->db->Quote($r->checked_out).",";       # checked_out
					$query .= $this->db->Quote($r->checked_out_time).",";  # checked_out_time
					$query .= $this->db->Quote($r->access).",";            # access
					$query .= $this->db->Quote($r->params).",";            # params
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
					$this->db->setQuery($query);
					$this->db->query();

					// Get last id
					$id = $this->db->insertid();

					// Set any categories that were in recently added section to have that new category id as parent_id
					$query = "UPDATE  `#__categories` SET parent_id = {$id}, level = 2 WHERE section = {$r->id};";
					$this->db->setQuery($query);
					$this->db->query();
				}
			}

			// Insert root category and set all 1st level categories to point to it
			$query  = "INSERT INTO `#__categories` (asset_id,parent_id,lft,rgt,level,path,extension,title,alias,note,description,published,checked_out,checked_out_time,access,params,metadesc,metakey,metadata,created_user_id,created_time,modified_user_id,modified_time,hits,language) VALUES ";
			$query .= "( 0, 0, 0, 0, 0, '', 'system', 'ROOT', 'root', '', '', 1, 0, '0000-00-00 00:00:00', 1, '{}', '', '', '', 0, '2009-10-18 16:07:09', 0, '0000-00-00 00:00:00', 0, '*');";
			$query .= "UPDATE  `#__categories` SET parent_id=LAST_INSERT_ID() WHERE parent_id=0 AND id !=LAST_INSERT_ID();";
			$this->db->setQuery($query);
			$this->db->query();

			// Fix up "path" field
			$query = "UPDATE `#__categories` SET `path` = alias WHERE (`path` IS NULL OR `path` = '') AND level = 1;";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "SELECT * FROM `#__categories` WHERE (`path` IS NULL OR `path` = '') AND level = 2;";
			$this->db->setQuery($query);
			$results = $this->db->loadObjectList();

			if (count($results) > 0)
			{
				foreach ($results as $r)
				{
					// Get the parent item alias
					$query = "SELECT `alias` FROM `#__categories` WHERE `id` = {$r->parent_id};";
					$this->db->setQuery($query);
					$alias = $this->db->loadResult();

					// Build path var
					$path = $alias . '/' . $r->alias;

					// Save the sub-category path
					$query = "UPDATE `#__categories` SET `path` = \"{$path}\" WHERE `id` = {$r->id};";
					$this->db->setQuery($query);
					$this->db->query();
				}
			}
		}

		if ($this->db->tableHasField('#__categories', 'ordering'))
		{
			$query = "ALTER TABLE `#__categories` DROP COLUMN `ordering`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasField('#__categories', 'image'))
		{
			$query = "ALTER TABLE `#__categories` DROP COLUMN `image`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasField('#__categories', 'image_position'))
		{
			$query = "ALTER TABLE `#__categories` DROP COLUMN `image_position`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasField('#__categories', 'editor'))
		{
			$query = "ALTER TABLE `#__categories` DROP COLUMN `editor`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasField('#__categories', 'count'))
		{
			$query = "ALTER TABLE `#__categories` DROP COLUMN `count`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasField('#__categories', 'name'))
		{
			$query = "ALTER TABLE `#__categories` DROP COLUMN `name`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasField('#__categories', 'section'))
		{
			$query = "ALTER TABLE `#__categories` DROP COLUMN `section`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
