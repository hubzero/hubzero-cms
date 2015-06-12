<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for changing 'group_id' into 'scope_id' and adding 'access' field
 **/
class Migration20141029112543ComBlog extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__blog_entries'))
		{
			if (!$this->db->tableHasField('#__blog_entries', 'access'))
			{
				$query = "ALTER TABLE `#__blog_entries` ADD `access` TINYINT(3)  NOT NULL  DEFAULT '0'";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasField('#__blog_entries', 'scope_id'))
			{
				$query = "ALTER TABLE `#__blog_entries` CHANGE `group_id` `scope_id` INT(11)  NOT NULL  DEFAULT '0';";
				$this->db->setQuery($query);
				$this->db->query();

				$query = "UPDATE `#__blog_entries` SET `scope_id`=`created_by` WHERE `scope`='member' AND `scope_id`=0";
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
		if ($this->db->tableExists('#__blog_entries'))
		{
			if ($this->db->tableHasField('#__blog_entries', 'access'))
			{
				$query = "ALTER TABLE `#__blog_entries` DROP `access`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__blog_entries', 'scope_id'))
			{
				$query = "ALTER TABLE `#__blog_entries` CHANGE `scope_id` `group_id` INT(11)  NOT NULL  DEFAULT '0';";
				$this->db->setQuery($query);
				$this->db->query();

				$query = "UPDATE `#__blog_entries` SET `group_id`='0' WHERE `scope`='member' AND `group_id`>0";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}