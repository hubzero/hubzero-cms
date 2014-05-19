<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for changing group_id to scope_id in forums
 **/
class Migration20121130000000ComForum extends Base
{
	public function up()
	{
		$query = '';

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

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}