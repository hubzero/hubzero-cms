<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding indices to wiki tables
 **/
class Migration20140829132200ComWiki extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__wiki_comments'))
		{
			if (!$this->db->tableHasKey('#__wiki_comments', 'idx_pageid'))
			{
				$query = "ALTER TABLE `#__wiki_comments` ADD INDEX `idx_pageid` (`pageid`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__wiki_comments', 'idx_version'))
			{
				$query = "ALTER TABLE `#__wiki_comments` ADD INDEX `idx_version` (`version`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__wiki_comments', 'idx_status'))
			{
				$query = "ALTER TABLE `#__wiki_comments` ADD INDEX `idx_status` (`status`);";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__wiki_attachments'))
		{
			if (!$this->db->tableHasKey('#__wiki_attachments', 'idx_pageid'))
			{
				$query = "ALTER TABLE `#__wiki_attachments` ADD INDEX `idx_pageid` (`pageid`);";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__wiki_page'))
		{
			if (!$this->db->tableHasKey('#__wiki_page', 'idx_group_cn'))
			{
				$query = "ALTER TABLE `#__wiki_page` ADD INDEX `idx_group_cn` (`group_cn`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__wiki_page', 'idx_state'))
			{
				$query = "ALTER TABLE `#__wiki_page` ADD INDEX `idx_state` (`state`);";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__wiki_version'))
		{
			if ($this->db->tableHasKey('#__wiki_version', 'jos_wiki_version_pageid_idx'))
			{
				$query = "ALTER TABLE `#__wiki_version` DROP INDEX `jos_wiki_version_pageid_idx`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__wiki_version', 'idx_pageid'))
			{
				$query = "ALTER TABLE `#__wiki_version` ADD INDEX `idx_pageid` (`pageid`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__wiki_version', 'idx_approved'))
			{
				$query = "ALTER TABLE `#__wiki_version` ADD INDEX `idx_approved` (`approved`);";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__wiki_page_author'))
		{
			if (!$this->db->tableHasKey('#__wiki_page_author', 'idx_page_id'))
			{
				$query = "ALTER TABLE `#__wiki_page_author` ADD INDEX `idx_page_id` (`page_id`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__wiki_page_author', 'idx_user_id'))
			{
				$query = "ALTER TABLE `#__wiki_page_author` ADD INDEX `idx_user_id` (`user_id`);";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__wiki_comments'))
		{
			if ($this->db->tableHasKey('#__wiki_comments', 'idx_pageid'))
			{
				$query = "ALTER TABLE `#__wiki_comments` DROP INDEX `idx_pageid`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__wiki_comments', 'idx_version'))
			{
				$query = "ALTER TABLE `#__wiki_comments` DROP INDEX `idx_version`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__wiki_comments', 'idx_status'))
			{
				$query = "ALTER TABLE `#__wiki_comments` DROP INDEX `idx_status`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__wiki_attachments'))
		{
			if ($this->db->tableHasKey('#__wiki_attachments', 'idx_pageid'))
			{
				$query = "ALTER TABLE `#__wiki_attachments` DROP INDEX `idx_pageid`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__wiki_page'))
		{
			if ($this->db->tableHasKey('#__wiki_page', 'idx_group_cn'))
			{
				$query = "ALTER TABLE `#__wiki_page` DROP INDEX `idx_group_cn`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__wiki_page', 'idx_state'))
			{
				$query = "ALTER TABLE `#__wiki_page` DROP INDEX `idx_state`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__wiki_version'))
		{
			if ($this->db->tableHasKey('#__wiki_version', 'idx_pageid'))
			{
				$query = "ALTER TABLE `#__wiki_version` DROP INDEX `idx_pageid`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__wiki_version', 'jos_wiki_version_pageid_idx'))
			{
				$query = "ALTER TABLE `#__wiki_version` ADD INDEX `jos_wiki_version_pageid_idx` (`pageid`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__wiki_version', 'idx_approved'))
			{
				$query = "ALTER TABLE `#__wiki_version` DROP INDEX `idx_approved`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__wiki_page_author'))
		{
			if ($this->db->tableHasKey('#__wiki_page_author', 'idx_page_id'))
			{
				$query = "ALTER TABLE `#__wiki_page_author` DROP INDEX `idx_page_id`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__wiki_page_author', 'idx_user_id'))
			{
				$query = "ALTER TABLE `#__wiki_page_author` DROP INDEX `idx_user_id` ;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}