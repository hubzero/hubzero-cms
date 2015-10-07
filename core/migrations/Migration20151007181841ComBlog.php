<?php

use Hubzero\Content\Migration\Base;

// Restricted access
defined('_HZEXEC_') or die();

/**
 * Migration script for making Blog Entry state and access conform to standard conventions
 **/
class Migration20151007181841ComBlog extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__blog_entries') && $this->db->tableHasField('#__blog_entries', 'access'))
		{
			// Public entries
			$query = "UPDATE `#__blog_entries` SET `access`=1 WHERE `state`=1";
			$this->db->setQuery($query);
			$this->db->query();

			// Registered entries
			$query = "UPDATE `#__blog_entries` SET `access`=2 WHERE `state`=2";
			$this->db->setQuery($query);
			$this->db->query();

			// Private entries
			$query = "UPDATE `#__blog_entries` SET `access`=5 WHERE `state`=0";
			$this->db->setQuery($query);
			$this->db->query();

			// All entries are "published"
			$query = "UPDATE `#__blog_entries` SET `state`=1 WHERE `state`>=0";
			$this->db->setQuery($query);
			$this->db->query();

			// Change the state of trashed entries
			$query = "UPDATE `#__blog_entries` SET `state`=2 WHERE `state`<0";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__blog_entries') && $this->db->tableHasField('#__blog_entries', 'access'))
		{
			// Public entries
			$query = "UPDATE `#__blog_entries` SET `state`=1 WHERE `access`=1";
			$this->db->setQuery($query);
			$this->db->query();

			// Registered entries
			$query = "UPDATE `#__blog_entries` SET `state`=2 WHERE `access`=2";
			$this->db->setQuery($query);
			$this->db->query();

			// Private entries
			$query = "UPDATE `#__blog_entries` SET `state`=0 WHERE `access`=5";
			$this->db->setQuery($query);
			$this->db->query();

			// Change the state of trashed entries
			$query = "UPDATE `#__blog_entries` SET `state`='-1' WHERE `state`=2";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}