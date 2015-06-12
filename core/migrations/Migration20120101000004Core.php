<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for 2011/12 table modifications
 **/
class Migration20120101000004Core extends Base
{
	public function up()
	{
		if ($this->db->tableExists('#__blog_entries')
			&& !$this->db->tableHasKey('#__blog_entries', 'ftidx_title_content')
			&& $this->db->tableHasField('#__blog_entries', 'title')
			&& $this->db->tableHasField('#__blog_entries', 'content'))
		{
			$query = "ALTER TABLE `#__blog_entries` ADD FULLTEXT INDEX `ftidx_title_content` (`title` ASC, `content` ASC)";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__citations'))
		{
			if ($this->db->tableHasField('#__citations', 'type') && $this->db->tableHasField('#__citations', 'uid'))
			{
				$query = "ALTER TABLE `#__citations` CHANGE COLUMN `type` `type` VARCHAR(30) NULL DEFAULT NULL AFTER `uid`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__citations', 'published') && $this->db->tableHasField('#__citations', 'type'))
			{
				$query = "ALTER TABLE `#__citations` CHANGE COLUMN `published` `published` INT(3) NOT NULL DEFAULT '1' AFTER `type`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasField('#__citations', 'language') && $this->db->tableHasField('#__citations', 'notes'))
			{
				$query = "ALTER TABLE `#__citations` ADD COLUMN `language` VARCHAR(100) NULL DEFAULT NULL AFTER `notes`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasField('#__citations', 'accession_number') && $this->db->tableHasField('#__citations', 'language'))
			{
				$query = "ALTER TABLE `#__citations` ADD COLUMN `accession_number` VARCHAR(100) NULL DEFAULT NULL AFTER `language`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasField('#__citations', 'short_title') && $this->db->tableHasField('#__citations', 'accession_number'))
			{
				$query = "ALTER TABLE `#__citations` ADD COLUMN `short_title` VARCHAR(250) NULL DEFAULT NULL AFTER `accession_number`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasField('#__citations', 'author_address') && $this->db->tableHasField('#__citations', 'short_title'))
			{
				$query = "ALTER TABLE `#__citations` ADD COLUMN `author_address` TEXT NULL DEFAULT NULL AFTER `short_title`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasField('#__citations', 'keywords') && $this->db->tableHasField('#__citations', 'author_address'))
			{
				$query = "ALTER TABLE `#__citations` ADD COLUMN `keywords` TEXT NULL DEFAULT NULL AFTER `author_address`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasField('#__citations', 'abstract') && $this->db->tableHasField('#__citations', 'keywords'))
			{
				$query = "ALTER TABLE `#__citations` ADD COLUMN `abstract` TEXT NULL DEFAULT NULL AFTER `keywords`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasField('#__citations', 'call_number') && $this->db->tableHasField('#__citations', 'abstract'))
			{
				$query = "ALTER TABLE `#__citations` ADD COLUMN `call_number` VARCHAR(100) NULL DEFAULT NULL AFTER `abstract`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasField('#__citations', 'label') && $this->db->tableHasField('#__citations', 'call_number'))
			{
				$query = "ALTER TABLE `#__citations` ADD COLUMN `label` VARCHAR(100) NULL DEFAULT NULL AFTER `call_number`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasField('#__citations', 'research_notes') && $this->db->tableHasField('#__citations', 'label'))
			{
				$query = "ALTER TABLE `#__citations` ADD COLUMN `research_notes` TEXT NULL DEFAULT NULL AFTER `label`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasField('#__citations', 'params') && $this->db->tableHasField('#__citations', 'research_notes'))
			{
				$query = "ALTER TABLE `#__citations` ADD COLUMN `params` TEXT NULL DEFAULT NULL AFTER `research_notes` ";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__citations', 'title')
				&& $this->db->tableHasField('#__citations', 'isbn')
				&& $this->db->tableHasField('#__citations', 'doi')
				&& $this->db->tableHasField('#__citations', 'abstract')
				&& !$this->db->tableHasKey('#__citations', 'ftidx_title_isbn_doi_abstract'))
			{
				$query = "ALTER TABLE `#__citations` ADD FULLTEXT INDEX `ftidx_title_isbn_doi_abstract` (`title` ASC, `isbn` ASC, `doi` ASC, `abstract` ASC)";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__citations_assoc'))
		{
			if ($this->db->tableHasField('#__citations_assoc', 'table') && !$this->db->tableHasField('#__citations_assoc', 'tbl'))
			{
				$query = "ALTER TABLE `#__citations_assoc` CHANGE COLUMN `table` `tbl` VARCHAR(50) NULL DEFAULT NULL AFTER `type`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__citations_authors'))
		{
			if ($this->db->tableHasField('#__citations_authors', 'author_uid') && !$this->db->tableHasField('#__citations_authors', 'authorid'))
			{
				$query = "ALTER TABLE `#__citations_authors` CHANGE COLUMN `author_uid` `authorid` INT(11) NULL DEFAULT '0' AFTER `author`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasField('#__citations_authors', 'uidNumber') && $this->db->tableHasField('#__citations_authors', 'authorid'))
			{
				$query = "ALTER TABLE `#__citations_authors` ADD COLUMN `uidNumber` INT(11) NULL DEFAULT '0' AFTER `authorid`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__citations_authors', 'cid')
				&& $this->db->tableHasField('#__citations_authors', 'author')
				&& $this->db->tableHasField('#__citations_authors', 'authorid')
				&& $this->db->tableHasField('#__citations_authors', 'uidNumber')
				&& !$this->db->tableHasKey('#__citations_authors', 'uidx_cid_author_authorid_uidNumber'))
			{
				$query = "ALTER TABLE `#__citations_authors` ADD UNIQUE INDEX `uidx_cid_author_authorid_uidNumber` (`cid` ASC, `author` ASC, `authorid` ASC, `uidNumber` ASC)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__citations_authors', 'authorid') && !$this->db->tableHasKey('#__citations_authors', 'idx_authorid'))
			{
				$query = "ALTER TABLE `#__citations_authors` ADD INDEX `authorid` (`idx_authorid` ASC)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__citations_authors', 'uidNumber') && !$this->db->tableHasKey('#__citations_authors', 'idx_uidNumber'))
			{
				$query = "ALTER TABLE `#__citations_authors` ADD INDEX `uidNumber` (`idx_uidNumber` ASC)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__citations_authors', 'cid_auth_uid'))
			{
				$query = "ALTER TABLE `#__citations_authors` DROP INDEX `cid_auth_uid`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__doi_mapping'))
		{
			if ($this->db->tableHasField('#__doi_mapping', 'alias') && !$this->db->tableHasField('#__doi_mapping', 'versionid'))
			{
				$query = "ALTER TABLE `#__doi_mapping` ADD COLUMN `versionid` INT(11) NULL DEFAULT '0' AFTER `alias`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__doi_mapping', 'versionid') && !$this->db->tableHasField('#__doi_mapping', 'doi'))
			{
				$query = "ALTER TABLE `#__doi_mapping` ADD COLUMN `doi` VARCHAR(50) NULL DEFAULT NULL AFTER `versionid`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__events') && $this->db->tableHasField('#__events', 'publish_down') && !$this->db->tableHasField('#__events', 'time_zone'))
		{
			$query = "ALTER TABLE `#__events` ADD COLUMN `time_zone` VARCHAR(5) NULL DEFAULT NULL AFTER `publish_down`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__faq'))
		{
			if ($this->db->tableHasField('#__faq', 'fulltext') && !$this->db->tableHasField('#__faq', 'fulltxt'))
			{
				$query = "ALTER TABLE `#__faq` CHANGE COLUMN `fulltext` `fulltxt` TEXT NULL DEFAULT NULL AFTER `params`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__faq', 'jos_faq_title_introtext_fulltext_ftidx'))
			{
				$query = "ALTER TABLE `#__faq` DROP INDEX `jos_faq_title_introtext_fulltext_ftidx`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__faq', 'title')
				&& $this->db->tableHasField('#__faq', 'params')
				&& $this->db->tableHasField('#__faq', 'fulltxt')
				&& !$this->db->tableHasKey('#__faq', 'jos_faq_title_introtext_fulltext_ftidx'))
			{
				$query = "ALTER TABLE `#__faq` ADD FULLTEXT INDEX `jos_faq_title_introtext_fulltext_ftidx` (`title` ASC, `params` ASC, `fulltxt` ASC)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__faq', 'fulltxt') && !$this->db->tableHasKey('#__faq', 'ftidx_fulltxt'))
			{
				$query = "ALTER TABLE `#__faq` ADD FULLTEXT INDEX `ftidx_fulltxt` (`fulltxt` ASC)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__faq', 'fulltext'))
			{
				$query = "ALTER TABLE `#__faq` DROP INDEX `fulltext`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__faq_categories'))
		{
			if ($this->db->tableHasField('#__faq_categories', 'description'))
			{
				$query = "ALTER TABLE `#__faq_categories` CHANGE COLUMN `description` `description` VARCHAR(255) NULL DEFAULT ''";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasField('#__faq_categories', 'asset_id') && $this->db->tableHasField('#__faq_categories', 'access'))
			{
				$query = "ALTER TABLE `#__faq_categories` ADD COLUMN `asset_id` INT(11) NOT NULL DEFAULT '0' AFTER `access`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__faq_comments'))
		{
			if ($this->db->tableHasField('#__faq_comments', 'entry_id'))
			{
				$query = "ALTER TABLE `#__faq_comments` CHANGE COLUMN `entry_id` `entry_id` INT(11) NOT NULL DEFAULT '0'";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__faq_comments', 'content'))
			{
				$query = "ALTER TABLE `#__faq_comments` CHANGE COLUMN `content` `content` TEXT NULL DEFAULT NULL";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__faq_comments', 'created'))
			{
				$query = "ALTER TABLE `#__faq_comments` CHANGE COLUMN `created` `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__faq_comments', 'created_by'))
			{
				$query = "ALTER TABLE `#__faq_comments` CHANGE COLUMN `created_by` `created_by` INT(11) NOT NULL DEFAULT '0'";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__faq_comments', 'anonymous'))
			{
				$query = "ALTER TABLE `#__faq_comments` CHANGE COLUMN `anonymous` `anonymous` TINYINT(2) NOT NULL DEFAULT '0'";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__faq_comments', 'parent'))
			{
				$query = "ALTER TABLE `#__faq_comments` CHANGE COLUMN `parent` `parent` INT(11) NOT NULL DEFAULT '0'";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__faq_comments', 'parent') && !$this->db->tableHasField('#__faq_comments', 'asset_id'))
			{
				$query = "ALTER TABLE `#__faq_comments` ADD COLUMN `asset_id` INT(11) NOT NULL DEFAULT '0' AFTER `parent`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__faq_comments', 'asset_id') && !$this->db->tableHasField('#__faq_comments', 'helpful'))
			{
				$query = "ALTER TABLE `#__faq_comments` ADD COLUMN `helpful` INT(11) NOT NULL DEFAULT '0' AFTER `asset_id`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__faq_comments', 'helpful') && !$this->db->tableHasField('#__faq_comments', 'nothelpful'))
			{
				$query = "ALTER TABLE `#__faq_comments` ADD COLUMN `nothelpful` INT(11) NOT NULL DEFAULT '0' AFTER `helpful`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__password_rule'))
		{
			if ($this->db->tableHasField('#__password_rule', 'group')
				&& !$this->db->tableHasField('#__password_rule', 'grp')
				&& $this->db->tableHasField('#__password_rule', 'failuremsg'))
			{
				$query = "ALTER TABLE `#__password_rule` CHANGE COLUMN `group` `grp` CHAR(32) NOT NULL AFTER `failuremsg` ;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if (!$this->db->tableExists('#__polls') && $this->db->tableExists('#__xpolls'))
		{
			$query = "RENAME TABLE `#__xpolls` TO `#__polls`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__poll_data') && $this->db->tableExists('#__xpoll_data'))
		{
			$query = "RENAME TABLE `#__xpoll_data` TO `#__poll_data`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__poll_date') && $this->db->tableExists('#__xpoll_date'))
		{
			$query = "RENAME TABLE `#__xpoll_date` TO `#__poll_date`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__poll_menu') && $this->db->tableExists('#__xpoll_menu'))
		{
			$query = "RENAME TABLE `#__xpoll_menu` TO `#__poll_menu`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__resource_types')
			&& $this->db->tableHasField('#__resource_types', 'id')
			&& !$this->db->tableHasField('#__resource_types', 'alias'))
		{
			$query = "ALTER TABLE `#__resource_types` ADD COLUMN `alias` VARCHAR(100) NULL DEFAULT NULL AFTER `id`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__resources'))
		{
			if ($this->db->tableHasField('#__resources', 'fulltext')
				&& !$this->db->tableHasField('#__resources', 'fulltxt')
				&& $this->db->tableHasField('#__resources', 'introtext'))
			{
				$query = "ALTER TABLE `#__resources` CHANGE COLUMN `fulltext` `fulltxt` TEXT NOT NULL AFTER `introtext`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__resources', 'introtext'))
			{
				$query = "ALTER TABLE `#__resources` DROP INDEX `introtext`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__resources', 'introtext')
				&& $this->db->tableHasField('#__resources', 'fulltxt')
				&& !$this->db->tableHasKey('#__resources', 'ftidx_introtext_fulltxt'))
			{
				$query = "ALTER TABLE `#__resources` ADD FULLTEXT INDEX `ftidx_introtext_fulltxt` (`introtext` ASC, `fulltxt` ASC)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__resources', 'jos_resources_title_introtext_fulltext_ftidx'))
			{
				$query = "ALTER TABLE `#__resources` DROP INDEX `jos_resources_title_introtext_fulltext_ftidx`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__resources', 'ftidx_title_introtext_fulltxt')
				&& $this->db->tableHasField('#__resources', 'title')
				&& $this->db->tableHasField('#__resources', 'introtext')
				&& $this->db->tableHasField('#__resources', 'fulltxt'))
			{
				$query = "ALTER TABLE `#__resources` ADD FULLTEXT INDEX `ftidx_title_introtext_fulltxt` (`title` ASC, `introtext` ASC, `fulltxt` ASC)";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__session'))
		{
			if ($this->db->tableHasKey('#__session', 'PRIMARY'))
			{
				$query = "ALTER TABLE `#__session` DROP PRIMARY KEY";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__session', 'PRIMARY') && $this->db->tableHasField('#__session', 'session_id'))
			{
				$query = "ALTER TABLE `#__session` ADD PRIMARY KEY USING BTREE (`session_id`)";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__stats_topvals'))
		{
			if ($this->db->tableHasField('#__stats_topvals', 'rank'))
			{
				$query = "ALTER TABLE `#__stats_topvals` CHANGE COLUMN `rank` `rank` SMALLINT(6) NOT NULL DEFAULT '0'";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__support_tickets'))
		{
			if (!$this->db->tableHasField('#__support_tickets', 'open') && $this->db->tableHasField('#__support_tickets', 'group'))
			{
				$query = "ALTER TABLE `#__support_tickets` ADD COLUMN `open` TINYINT(3) NOT NULL DEFAULT '1' AFTER `group`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__tags'))
		{
			if ($this->db->tableHasField('#__tags', 'alias'))
			{
				$query = "ALTER TABLE `#__tags` DROP COLUMN `alias`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__tags', 'jos_tags_raw_tag_alias_description_ftidx'))
			{
				$query = "ALTER TABLE `#__tags` DROP INDEX `jos_tags_raw_tag_alias_description_ftidx`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__tags', 'jos_tags_raw_tag_alias_description_ftidx')
				&& $this->db->tableHasField('#__tags', 'raw_tag')
				&& $this->db->tableHasField('#__tags', 'description'))
				$query = "ALTER TABLE `#__tags` ADD FULLTEXT INDEX `jos_tags_raw_tag_alias_description_ftidx` (`raw_tag` ASC, `description` ASC)";
				$this->db->setQuery($query);
				$this->db->query();
		}

		if ($this->db->tableExists('#__tags_object'))
		{
			if (!$this->db->tableHasField('#__tags_object', 'label') && $this->db->tableHasField('#__tags_object', 'tbl'))
			{
				$query = "ALTER TABLE `#__tags_object` ADD COLUMN `label` VARCHAR(30) NULL DEFAULT NULL AFTER `tbl`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__tags_object', 'jos_tags_object_objectid_tbl_idx')
				&& $this->db->tableHasField('#__tags_object', 'objectid')
				&& $this->db->tableHasField('#__tags_object', 'tbl'))
			{
				$query = "ALTER TABLE `#__tags_object` ADD INDEX `jos_tags_object_objectid_tbl_idx` (`objectid` ASC, `tbl` ASC)";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__tool'))
		{
			if ($this->db->tableHasField('#__tool', 'fulltext')
				&& $this->db->tableHasField('#__tool', 'description')
				&& !$this->db->tableHasField('#__tool', 'fulltxt'))
			{
				$query = "ALTER TABLE `#__tool` CHANGE COLUMN `fulltext` `fulltxt` TEXT NULL DEFAULT NULL AFTER `description`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__tool_version'))
		{
			if ($this->db->tableHasField('#__tool_version', 'fulltext')
				&& $this->db->tableHasField('#__tool_version', 'description')
				&& !$this->db->tableHasField('#__tool_version', 'fulltxt'))
			{
				$query = "ALTER TABLE `#__tool_version` CHANGE COLUMN `fulltext` `fulltxt` TEXT NULL DEFAULT NULL AFTER `description`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__tool_version', 'priority') && !$this->db->tableHasField('#__tool_version', 'params'))
			{
				$query = "ALTER TABLE `#__tool_version` ADD COLUMN `params` TEXT NULL DEFAULT NULL AFTER `priority`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__users_password'))
		{
			if ($this->db->tableHasField('#__users_password', 'user_id'))
			{
				$query = "ALTER TABLE `#__users_password` CHANGE COLUMN `user_id` `user_id` INT(11) NOT NULL FIRST";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__users_password_history'))
		{
			if ($this->db->tableHasField('#__users_password_history', 'user_id') && $this->db->tableHasField('#__users_password_history', 'id'))
			{
				$query = "ALTER TABLE `#__users_password_history` CHANGE COLUMN `user_id` `user_id` INT(11) NOT NULL AFTER `id`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__users_password_history', 'passhash') && $this->db->tableHasField('#__users_password_history', 'user_id'))
			{
				$query = "ALTER TABLE `#__users_password_history` CHANGE COLUMN `passhash` `passhash` CHAR(32) NOT NULL AFTER `user_id`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__users_password_history', 'PRIMARY') && $this->db->getPrimaryKey('#__users_password_history') == 'user_id')
			{
				$query = "ALTER TABLE `#__users_password_history` DROP PRIMARY KEY";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasField('#__users_password_history', 'id'))
			{
				$query = "ALTER TABLE `#__users_password_history` ADD COLUMN `id` INT(11) NOT NULL FIRST";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__users_password_history', 'PRIMARY') && $this->db->tableHasField('#__users_password_history', 'id'))
			{
				$query = "ALTER TABLE `#__users_password_history` ADD PRIMARY KEY (`id`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__users_password_history', 'id'))
			{
				$query = "ALTER TABLE `#__users_password_history` CHANGE COLUMN `id` `id` INT(11) NOT NULL AUTO_INCREMENT FIRST";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__users_transactions'))
		{
			if (!$this->db->tableHasKey('#__users_transactions', 'idx_referenceid_category_type')
				&& $this->db->tableHasField('#__users_transactions', 'referenceid')
				&& $this->db->tableHasField('#__users_transactions', 'category')
				&& $this->db->tableHasField('#__users_transactions', 'type'))
			{
				$query = "ALTER TABLE `#__users_transactions` ADD INDEX `idx_referenceid_category_type` (`referenceid` ASC, `category` ASC, `type` ASC)";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__vote_log')
			&& $this->db->tableHasField('#__vote_log', 'referenceid')
			&& !$this->db->tableHasKey('#__vote_log', 'idx_referenceid'))
		{
			$query = "ALTER TABLE `#__vote_log` ADD INDEX `idx_referenceid` (`referenceid` ASC)";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__wiki_math'))
		{
			if ($this->db->tableHasField('#__wiki_math', 'inputhash'))
			{
				$query = "ALTER TABLE `#__wiki_math` CHANGE COLUMN `inputhash` `inputhash` VARCHAR(32) NOT NULL DEFAULT ''";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__wiki_math', 'outputhash'))
			{
				$query = "ALTER TABLE `#__wiki_math` CHANGE COLUMN `outputhash` `outputhash` VARCHAR(32) NOT NULL DEFAULT ''";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__wiki_page')
			&& !$this->db->tableHasField('#__wiki_page', 'group_cn')
			&& $this->db->tableHasField('#__wiki_page', 'group')
			&& $this->db->tableHasField('#__wiki_page', 'access'))
		{
			$query = "ALTER TABLE `#__wiki_page` CHANGE COLUMN `group` `group_cn` VARCHAR(255) NULL DEFAULT NULL AFTER `access`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__wiki_version')
			&& $this->db->tableHasField('#__wiki_version', 'pageid')
			&& !$this->db->tableHasKey('#__wiki_version', 'idx_pageid'))
		{
			$query = "ALTER TABLE `#__wiki_version` ADD INDEX `idx_pageid` (`pageid` ASC)";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__wishlist_item')
			&& $this->db->tableHasField('#__wishlist_item', 'wishlist')
			&& !$this->db->tableHasKey('#__wishlist_item', 'idx_wishlist'))
		{
			$query = "ALTER TABLE `#__wishlist_item` ADD INDEX `idx_wishlist` (`wishlist` ASC)";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__wishlist_vote')
			&& $this->db->tableHasField('#__wishlist_vote', 'wishid')
			&& !$this->db->tableHasKey('#__wishlist_vote', 'idx_wishid'))
		{
			$query = "ALTER TABLE `#__wishlist_vote` ADD INDEX `idx_wishid` (`wishid` ASC)";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__xgroups'))
		{
			if ($this->db->tableHasField('#__xgroups', 'privacy') && !$this->db->tableHasField('#__xgroups', 'discussion_email_autosubscribe'))
			{
				$query = "ALTER TABLE `#__xgroups` ADD COLUMN `discussion_email_autosubscribe` TINYINT(3) NULL DEFAULT NULL AFTER `privacy`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__xgroups', 'plugins') && !$this->db->tableHasField('#__xgroups', 'created'))
			{
				$query = "ALTER TABLE `#__xgroups` ADD COLUMN `created` DATETIME NULL DEFAULT NULL AFTER `plugins`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__xgroups', 'created') && !$this->db->tableHasField('#__xgroups', 'created_by'))
			{
				$query = "ALTER TABLE `#__xgroups` ADD COLUMN `created_by` INT(11) NULL DEFAULT NULL AFTER `created`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__xgroups', 'created_by') && !$this->db->tableHasField('#__xgroups', 'params'))
			{
				$query = "ALTER TABLE `#__xgroups` ADD COLUMN `params` TEXT NULL DEFAULT NULL AFTER `created_by`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__xgroups_events') && $this->db->tableHasField('#__xgroups_events', 'active'))
		{
			$query = "ALTER TABLE `#__xgroups_events` CHANGE COLUMN `active` `active` TINYINT(1) NOT NULL";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__xgroups_pages')
			&& $this->db->tableHasField('#__xgroups_pages', 'active')
			&& !$this->db->tableHasField('#__xgroups_pages', 'privacy'))
		{
			$query = "ALTER TABLE `#__xgroups_pages` ADD COLUMN `privacy` VARCHAR(10) NULL DEFAULT NULL AFTER `active`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__xgroups_tracperm'))
		{
			if ($this->db->tableHasField('#__xgroups_tracperm', 'group_id')
				&& $this->db->tableHasField('#__xgroups_tracperm', 'action')
				&& !$this->db->tableHasKey('#__xgroups_tracperm', 'id'))
			{
				$query = "ALTER TABLE `#__xgroups_tracperm` ADD UNIQUE INDEX `id` (`group_id` ASC, `action` ASC)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__xgroups_tracperm', 'PRIMARY'))
			{
				$query = "ALTER TABLE `#__xgroups_tracperm` DROP PRIMARY KEY";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__xprofiles')
			&& $this->db->tableHasField('#__xprofiles', 'shadowExpire')
			&& !$this->db->tableHasField('#__xprofiles', 'locked'))
		{
			$query = "ALTER TABLE `#__xprofiles` ADD COLUMN `locked` TINYINT(4) NOT NULL DEFAULT '0' AFTER `shadowExpire`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__ysearch_site_map'))
		{
			if (!$this->db->tableHasKey('#__ysearch_site_map', 'ftidx_title_description')
				&& $this->db->tableHasField('#__ysearch_site_map', 'title')
				&& $this->db->tableHasField('#__ysearch_site_map', 'description'))
			{
				$query = "ALTER TABLE `#__ysearch_site_map` ADD FULLTEXT INDEX `ftidx_title_description` (`title` ASC, `description` ASC)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__ysearch_site_map', 'ysearch_site_map_title_description_ftidx'))
			{
				$query = "ALTER TABLE `#__ysearch_site_map` DROP INDEX `ysearch_site_map_title_description_ftidx`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__ysearch_site_map', 'jos_ysearch_site_map_title_description_ftidx'))
			{
				$query = "ALTER TABLE `#__ysearch_site_map` DROP INDEX `jos_ysearch_site_map_title_description_ftidx`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
