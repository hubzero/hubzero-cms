<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for standardizing table and column names for wiki
 **/
class Migration20160508203200ComWiki extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__wiki_page') && !$this->db->tableExists('#__wiki_pages'))
		{
			$query = "RENAME TABLE `#__wiki_page` TO `#__wiki_pages`";
			$this->db->setQuery($query);
			$this->db->query();

			if (!$this->db->tableHasField('#__wiki_pages', 'scope_id'))
			{
				$query = "ALTER TABLE `#__wiki_pages` ADD COLUMN `scope_id` INT(11) NOT NULL DEFAULT 0 AFTER `scope`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasField('#__wiki_pages', 'path'))
			{
				$query = "ALTER TABLE `#__wiki_pages` ADD COLUMN `path` VARCHAR(255) NOT NULL AFTER `pagename`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasField('#__wiki_pages', 'namespace'))
			{
				$query = "ALTER TABLE `#__wiki_pages` ADD COLUMN `namespace` VARCHAR(255) NOT NULL AFTER `id`;";
				$this->db->setQuery($query);
				$this->db->query();

				$query = "UPDATE `#__wiki_pages` SET `namespace`='Help' WHERE `pagename` LIKE 'Help:%'";
				$this->db->setQuery($query);
				$this->db->query();

				$query = "UPDATE `#__wiki_pages` SET `namespace`='Template' WHERE `pagename` LIKE 'Template:%'";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasField('#__wiki_pages', 'protected'))
			{
				$query = "ALTER TABLE `#__wiki_pages` ADD COLUMN `protected` TINYINT(2) NOT NULL DEFAULT 0;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasField('#__wiki_pages', 'parent'))
			{
				$query = "ALTER TABLE `#__wiki_pages` ADD COLUMN `parent` INT(11) NOT NULL DEFAULT 0;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			// Move state=1 (locked) to proected column
			$query = "UPDATE `#__wiki_pages` SET `protected`=1 WHERE `state`=1;";
			$this->db->setQuery($query);
			$this->db->query();

			// Mark items as published (state=1)
			$query = "UPDATE `#__wiki_pages` SET `state`=1 WHERE `state`=0;";
			$this->db->setQuery($query);
			$this->db->query();

			if ($this->db->tableHasField('#__wiki_pages', 'group_cn'))
			{
				// Convert group pages
				$query = "SELECT w.id, w.scope, w.group_cn, g.gidNumber FROM `#__wiki_pages` AS w INNER JOIN `#__xgroups` AS g ON w.`group_cn`=g.`cn`";
				$this->db->setQuery($query);
				$rows = $this->db->loadObjectList();
				foreach ($rows as $row)
				{
					$row->scope = substr($row->scope, strlen($row->group_cn . '/wiki'));
					$row->scope = ltrim($row->scope, '/');

					$query = "UPDATE `#__wiki_pages`
						SET `scope`='group', `scope_id`=" . $this->db->quote($row->gidNumber) . ", `path`=" . $this->db->quote($row->scope) . "
						WHERE `id`=" . $this->db->quote($row->id);
					$this->db->setQuery($query);
					$this->db->query();
				}

				// Convert projects
				$query = "SELECT w.id, w.scope, w.group_cn FROM `#__wiki_pages` AS w WHERE w.group_cn LIKE 'pr-%'";
				$this->db->setQuery($query);
				$rows = $this->db->loadObjectList();
				foreach ($rows as $row)
				{
					$row->scope = substr($row->scope, strlen($row->group_cn . '/wiki'));
					$row->scope = ltrim($row->scope, '/');
					$row->group_cn = substr($row->group_cn, strlen('pr-'));

					$query = "SELECT id FROM `#__projects` WHERE alias=" . $this->db->quote($row->group_cn);
					$this->db->setQuery($query);
					$row->pidNumber = $this->db->loadResult();

					$query = "UPDATE `#__wiki_pages`
						SET `scope`='project', `scope_id`=" . $this->db->quote($row->pidNumber) . ", `path`=" . $this->db->quote($row->scope) . "
						WHERE `id`=" . $this->db->quote($row->id);
					$this->db->setQuery($query);
					$this->db->query();
				}

				$query = "UPDATE `#__wiki_pages` SET `scope`='site' WHERE `scope_id`=0";
				$this->db->setQuery($query);
				$this->db->query();

				// Drop column
				$query = "ALTER TABLE `#__wiki_pages` DROP COLUMN `group_cn`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__wiki_pages', 'authors'))
			{
				$query = "ALTER TABLE `#__wiki_pages` DROP COLUMN `authors`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__wiki_attachments'))
		{
			if ($this->db->tableHasField('#__wiki_attachments', 'pageid')
			 && !$this->db->tableHasField('#__wiki_attachments', 'page_id'))
			{
				$query = "ALTER TABLE `#__wiki_attachments` CHANGE `pageid` `page_id` int(11) default 0;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__wiki_page_author') && !$this->db->tableExists('#__wiki_authors'))
		{
			$query = "RENAME TABLE `#__wiki_page_author` TO `#__wiki_authors`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__wiki_math') && !$this->db->tableExists('#__wiki_formulas'))
		{
			$query = "RENAME TABLE `#__wiki_math` TO `#__wiki_formulas`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__wiki_page_links') && !$this->db->tableExists('#__wiki_links'))
		{
			$query = "RENAME TABLE `#__wiki_page_links` TO `#__wiki_links`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__wiki_comments'))
		{
			if ($this->db->tableHasField('#__wiki_comments', 'pageid'))
			{
				$query = "ALTER TABLE `#__wiki_comments` CHANGE `pageid` `page_id` int(11) default 0;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__wiki_comments', 'status'))
			{
				$query = "ALTER TABLE `#__wiki_comments` CHANGE `status` `state` tinyint(1) NOT NULL default 0;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__wiki_log'))
		{
			if ($this->db->tableHasField('#__wiki_log', 'pid'))
			{
				$query = "ALTER TABLE `#__wiki_log` CHANGE `pid` `page_id` int(11) NOT NULL default 0;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__wiki_log', 'uid'))
			{
				$query = "ALTER TABLE `#__wiki_log` CHANGE `uid` `user_id` int(11) default 0;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableExists('#__wiki_logs'))
			{
				$query = "RENAME TABLE `#__wiki_log` TO `#__wiki_logs`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__wiki_page_metrics'))
		{
			if ($this->db->tableHasField('#__wiki_page_metrics', 'pageid'))
			{
				$query = "ALTER TABLE `#__wiki_page_metrics` CHANGE `pageid` `page_id` int(11) NOT NULL default 0;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableExists('#__wiki_metrics'))
			{
				$query = "RENAME TABLE `#__wiki_page_metrics` TO `#__wiki_metrics`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__wiki_version'))
		{
			if ($this->db->tableHasField('#__wiki_version', 'pageid'))
			{
				$query = "ALTER TABLE `#__wiki_version` CHANGE `pageid` `page_id` int(11) NOT NULL default 0;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableExists('#__wiki_versions'))
			{
				$query = "RENAME TABLE `#__wiki_version` TO `#__wiki_versions`";
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
		if ($this->db->tableExists('#__wiki_page') && !$this->db->tableExists('#__wiki_pages'))
		{
			if ($this->db->tableHasField('#__wiki_pages', 'scope_id'))
			{
				$query = "ALTER TABLE `#__wiki_pages` DROP COLUMN `scope_id`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__wiki_pages', 'path'))
			{
				$query = "ALTER TABLE `#__wiki_pages` DROP COLUMN `path`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__wiki_pages', 'namespace'))
			{
				$query = "ALTER TABLE `#__wiki_pages` DROP COLUMN `namespace`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			// Mark items as published (state=1)
			$query = "UPDATE `#__wiki_pages` SET `state`=0 WHERE `state`=1;";
			$this->db->setQuery($query);
			$this->db->query();

			if ($this->db->tableHasField('#__wiki_pages', 'protected'))
			{
				// Mark items as published (state=1)
				$query = "UPDATE `#__wiki_pages` SET `state`=1 WHERE `protected`=1;";
				$this->db->setQuery($query);
				$this->db->query();

				$query = "ALTER TABLE `#__wiki_pages` DROP COLUMN `protected`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__wiki_pages', 'parent'))
			{
				$query = "ALTER TABLE `#__wiki_pages` DROP COLUMN `parent`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__wiki_pages', 'scope'))
			{
				if (!$this->db->tableHasField('#__wiki_pages', 'group_cn'))
				{
					$query = "ALTER TABLE `#__wiki_pages` ADD COLUMN `group_cn` VARCHAR(255);";
					$this->db->setQuery($query);
					$this->db->query();
				}

				// Convert group pages
				$query = "SELECT w.id, w.path, w.scope, w.scope_id, g.cn FROM `#__wiki_pages` AS w INNER JOIN `#__xgroups` AS g ON w.`scope_id`=g.`gidNumber` AND w.scope='group'";
				$this->db->setQuery($query);
				$rows = $this->db->loadObjectList();
				foreach ($rows as $row)
				{
					$query = "UPDATE `#__wiki_pages`
						SET `group_cn`=" . $this->db->quote($row->cn) . " AND `scope`=" . $this->db->quote($row->cn . '/wiki' . ($row->path ? '/' . $row->path : '')) . "
						WHERE `id`=" . $this->db->quote($row->id);
					$this->db->setQuery($query);
					$this->db->query();
				}

				// Convert projects
				$query = "SELECT w.id, w.path, w.scope, w.scope_id, p.alias FROM `#__wiki_pages` AS w INNER JOIN `#__projects` AS p ON w.scope_id=p.id AND w.scope='project'";
				$this->db->setQuery($query);
				$rows = $this->db->loadObjectList();
				foreach ($rows as $row)
				{
					$query = "UPDATE `#__wiki_pages`
						SET `group_cn`=" . $this->db->quote('pre-' . $row->alias) . " AND `scope`=" . $this->db->quote($row->alias . '/wiki' . ($row->path ? '/' . $row->path : '')) . "
						WHERE `id`=" . $this->db->quote($row->id);
					$this->db->setQuery($query);
					$this->db->query();
				}

				$query = "UPDATE `#__wiki_pages` SET `scope`=`path` WHERE `scope`='site' AND `scope_id`=0";
				$this->db->setQuery($query);
				$this->db->query();

				// Drop column
				$query = "ALTER TABLE `#__wiki_pages` DROP COLUMN `scope_id`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableExists('#__wiki_page'))
			{
				$query = "RENAME TABLE `#__wiki_pages` TO `#__wiki_page`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__wiki_attachments'))
		{
			if ($this->db->tableHasField('#__wiki_attachments', 'page_id'))
			{
				$query = "ALTER TABLE `#__wiki_attachments` CHANGE `page_id` `pageid` int(11) default 0;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__wiki_authors') && !$this->db->tableExists('#__wiki_page_author'))
		{
			$query = "RENAME TABLE `#__wiki_authors` TO `#__wiki_page_author`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__wiki_formulas') && !$this->db->tableExists('#__wiki_math'))
		{
			$query = "RENAME TABLE `#__wiki_formulas` TO `#__wiki_math`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__wiki_links') && !$this->db->tableExists('#__wiki_page_links'))
		{
			$query = "RENAME TABLE `#__wiki_links` TO `#__wiki_page_links`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__wiki_comments'))
		{
			if ($this->db->tableHasField('#__wiki_comments', 'page_id'))
			{
				$query = "ALTER TABLE `#__wiki_comments` CHANGE `page_id` `pageid` int(11) default 0;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__wiki_comments', 'state'))
			{
				$query = "ALTER TABLE `#__wiki_comments` CHANGE `state` `status` tinyint(1) NOT NULL default 0;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__wiki_logs'))
		{
			if ($this->db->tableHasField('#__wiki_logs', 'page_id'))
			{
				$query = "ALTER TABLE `#__wiki_logs` CHANGE `page_id` `pid` int(11) NOT NULL default 0;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__wiki_logs', 'user_id'))
			{
				$query = "ALTER TABLE `#__wiki_logs` CHANGE `user_id` `uid` int(11) default 0;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableExists('#__wiki_log'))
			{
				$query = "RENAME TABLE `#__wiki_logs` TO `#__wiki_log`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__wiki_metrics'))
		{
			if ($this->db->tableHasField('#__wiki_metrics', 'page_id'))
			{
				$query = "ALTER TABLE `#__wiki_metrics` CHANGE `page_id` `pageid` int(11) NOT NULL default 0;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableExists('#__wiki_page_metrics'))
			{
				$query = "RENAME TABLE `#__wiki_metrics` TO `#__wiki_page_metrics`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__wiki_versions'))
		{
			if ($this->db->tableHasField('#__wiki_versions', 'page_id'))
			{
				$query = "ALTER TABLE `#__wiki_versions` CHANGE `page_id` `pageid` int(11) NOT NULL default 0;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableExists('#__wiki_version'))
			{
				$query = "RENAME TABLE `#__wiki_versions` TO `#__wiki_version`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}