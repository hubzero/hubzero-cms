<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for fixing project notes that got assigned as group wiki pages
 **/
class Migration20160824181200ComWiki extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__wiki_pages'))
		{
			// Convert group pages
			$query = "SELECT w.*, g.cn FROM `#__wiki_pages` AS w LEFT JOIN `#__xgroups` AS g ON w.`scope_id`=g.`gidNumber` WHERE w.`path` LIKE '%/notes%' AND w.`scope`='group'";
			$this->db->setQuery($query);
			$rows = $this->db->loadObjectList();
			foreach ($rows as $row)
			{
				if (substr($row->cn, 0, strlen('pr-')) != 'pr-')
				{
					continue;
				}

				$path = array();
				$start = false;
				$p = explode('/', $row->path);
				foreach ($p as $s)
				{
					if ($s == 'notes')
					{
						$start = true;
						continue;
					}
					if ($start)
					{
						$path[] = $s;
					}
				}
				$row->path  = implode('/', $path);
				$row->scope = 'project';

				$project = substr($row->cn, strlen('pr-'));

				$query = "SELECT id FROM `#__projects` WHERE alias=" . $this->db->quote($project);
				$this->db->setQuery($query);
				$row->pidNumber = $this->db->loadResult();

				$query = "UPDATE `#__wiki_pages`
					SET `scope`='project', `scope_id`=" . $this->db->quote($row->pidNumber) . ", `path`=" . $this->db->quote($row->path) . "
					WHERE `id`=" . $this->db->quote($row->id);
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
	}
}