<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding indexes to #__citations tables
 **/
class Migration20170124191209ComCitations extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__citations_authors'))
		{
			if (!$this->db->tableHasKey('#__citations_authors', 'idx_cid'))
			{
				$query = "ALTER IGNORE TABLE `#__citations_authors` ADD INDEX `idx_cid` (`cid`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__citations_authors', 'idx_authorid'))
			{
				$query = "ALTER IGNORE TABLE `#__citations_authors` ADD INDEX `idx_authorid` (`authorid`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__citations_authors', 'idx_uidNumber'))
			{
				$query = "ALTER IGNORE TABLE `#__citations_authors` ADD INDEX `idx_uidNumber` (`uidNumber`)";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__citations_format'))
		{
			if (!$this->db->tableHasKey('#__citations_format', 'idx_typeid'))
			{
				$query = "ALTER IGNORE TABLE `#__citations_format` ADD INDEX `idx_typeid` (`typeid`)";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__citations_links'))
		{
			if (!$this->db->tableHasKey('#__citations_links', 'idx_citation_id'))
			{
				$query = "ALTER IGNORE TABLE `#__citations_links` ADD INDEX `idx_citation_id` (`citation_id`)";
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
		if ($this->db->tableExists('#__citations_authors'))
		{
			if ($this->db->tableHasKey('#__citations_authors', 'idx_cid'))
			{
				$query = "ALTER TABLE `#__citations_authors` DROP KEY `idx_cid`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__citations_authors', 'idx_authorid'))
			{
				$query = "ALTER TABLE `#__citations_authors` DROP KEY `idx_authorid`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__citations_authors', 'idx_uidNumber'))
			{
				$query = "ALTER TABLE `#__citations_authors` DROP KEY `idx_uidNumber`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__citations_format'))
		{
			if ($this->db->tableHasKey('#__citations_format', 'idx_typeid'))
			{
				$query = "ALTER TABLE `#__citations_format` DROP KEY `idx_typeid`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__citations_links'))
		{
			if ($this->db->tableHasKey('#__citations_links', 'idx_citation_id'))
			{
				$query = "ALTER TABLE `#__citations_links` DROP KEY `idx_citation_id`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
