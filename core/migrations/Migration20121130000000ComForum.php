<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for changing group_id to scope_id in forums
 **/
class Migration20121130000000ComForum extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query = '';

		if ($this->db->tableExists('#__forum_sections'))
		{
			if (!$this->db->tableHasField('#__forum_sections', 'scope_id') && $this->db->tableHasField('#__forum_sections', 'group_id'))
			{
				$query .= "ALTER TABLE `#__forum_sections` CHANGE `group_id` `scope_id` INT(11)  NOT NULL  DEFAULT '0';\n";
			}
			if (!$this->db->tableHasField('#__forum_sections', 'scope'))
			{
				$query .= "ALTER TABLE `#__forum_sections` ADD `scope` VARCHAR(100)  NOT NULL  DEFAULT 'site'  AFTER `state`;\n";

				$query .= "UPDATE `#__forum_sections` SET scope='group' WHERE scope_id>0 AND scope!='course'\n;";
				//$query .= "UPDATE `#__forum_sections` SET scope=CASE WHEN scope IN ('', 'group') THEN 'group' ELSE 'course' END WHERE scope_id>0;\n";
				$query .= "UPDATE `#__forum_sections` SET scope='site' WHERE scope_id=0;\n";
			}
		}
		if ($this->db->tableExists('#__forum_categories'))
		{
			if (!$this->db->tableHasField('#__forum_categories', 'scope_id') && $this->db->tableHasField('#__forum_categories', 'group_id'))
			{
				$query .= "ALTER TABLE `#__forum_categories` CHANGE `group_id` `scope_id` INT(11)  NOT NULL  DEFAULT '0';\n";
			}
			if (!$this->db->tableHasField('#__forum_categories', 'scope'))
			{
				$query .= "ALTER TABLE `#__forum_categories` ADD `scope` VARCHAR(100)  NOT NULL  DEFAULT 'site'  AFTER `state`;\n";

				$query .= "UPDATE `#__forum_categories` SET scope='group' WHERE scope_id>0 AND scope!='course';\n";
				//$query .= "UPDATE `#__forum_categories` SET scope=CASE WHEN scope IN ('', 'group') THEN 'group' ELSE 'course' END WHERE scope_id>0;\n";
				$query .= "UPDATE `#__forum_categories` SET scope='site' WHERE scope_id=0;\n";
			}
		}
		if ($this->db->tableExists('#__forum_posts'))
		{
			if (!$this->db->tableHasField('#__forum_posts', 'scope_id') && $this->db->tableHasField('#__forum_posts', 'group_id'))
			{
				$query .= "ALTER TABLE `#__forum_posts` CHANGE `group_id` `scope_id` INT(11)  NOT NULL  DEFAULT '0';\n";
			}
			if (!$this->db->tableHasField('#__forum_posts', 'scope'))
			{
				$query .= "ALTER TABLE `#__forum_posts` ADD `scope` VARCHAR(100)  NOT NULL  DEFAULT 'site'  AFTER `hits`;\n";

				$query .= "UPDATE `#__forum_posts` SET scope='group' WHERE scope_id>0 AND scope!='course';\n";
				//$query .= "UPDATE `#__forum_posts` SET scope=CASE WHEN scope IN ('', 'group') THEN 'group' ELSE 'course' END WHERE scope_id>0;\n";
				$query .= "UPDATE `#__forum_posts` SET scope='site' WHERE scope_id=0;\n";
			}
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$query = '';

		if ($this->db->tableExists('#__forum_sections'))
		{
			if ($this->db->tableHasField('#__forum_sections', 'scope_id') && !$this->db->tableHasField('#__forum_sections', 'group_id'))
			{
				$query .= "ALTER TABLE `#__forum_sections` CHANGE `scope_id` `group_id` INT(11)  NOT NULL  DEFAULT '0';\n";
			}
			if ($this->db->tableHasField('#__forum_sections', 'scope'))
			{
				$query .= "ALTER TABLE `#__forum_sections` DROP COLUMN `scope`;\n";
			}
		}
		if ($this->db->tableExists('#__forum_categories'))
		{
			if ($this->db->tableHasField('#__forum_categories', 'scope_id') && !$this->db->tableHasField('#__forum_categories', 'group_id'))
			{
				$query .= "ALTER TABLE `#__forum_categories` CHANGE `scope_id` `group_id` INT(11)  NOT NULL  DEFAULT '0';\n";
			}
			if ($this->db->tableHasField('#__forum_categories', 'scope'))
			{
				$query .= "ALTER TABLE `#__forum_categories` DROP COLUMN `scope`;\n";
			}
		}
		if ($this->db->tableExists('#__forum_posts'))
		{
			if ($this->db->tableHasField('#__forum_posts', 'scope_id') && !$this->db->tableHasField('#__forum_posts', 'group_id'))
			{
				$query .= "ALTER TABLE `#__forum_posts` CHANGE `scope_id` `group_id` INT(11)  NOT NULL  DEFAULT '0';\n";
			}
			if ($this->db->tableHasField('#__forum_posts', 'scope'))
			{
				$query .= "ALTER TABLE `#__forum_posts` DROP COLUMN `scope`;\n";
			}
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
