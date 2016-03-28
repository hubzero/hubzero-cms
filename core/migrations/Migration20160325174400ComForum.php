<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for updating access values and normalizing state column names
 **/
class Migration20160325174400ComForum extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__forum_sections')
		 && $this->db->tableHasField('#__forum_sections', 'access'))
		{
			$query = "UPDATE `#__forum_sections` SET `access`=(`access` + 1)";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__forum_categories')
		 && $this->db->tableHasField('#__forum_categories', 'access'))
		{
			$query = "UPDATE `#__forum_categories` SET `access`=(`access` + 1)";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__forum_posts')
		 && $this->db->tableHasField('#__forum_posts', 'access'))
		{
			$query = "UPDATE `#__forum_posts` SET `access`=(`access`+ 1)";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__forum_attachments')
		 && $this->db->tableHasField('#__forum_attachments', 'status'))
		{
			$query = "ALTER TABLE `#__forum_attachments` CHANGE `status` `state` int(3) NOT NULL default 0;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__forum_sections')
		 && $this->db->tableHasField('#__forum_sections', 'access'))
		{
			$query = "UPDATE `#__forum_sections` SET `access`=(`access` - 1)";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__forum_categories')
		 && $this->db->tableHasField('#__forum_categories', 'access'))
		{
			$query = "UPDATE `#__forum_categories` SET `access`=(`access` - 1)";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__forum_posts')
		 && $this->db->tableHasField('#__forum_posts', 'access'))
		{
			$query = "UPDATE `#__forum_posts` SET `access`=(`access` - 1)";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__forum_attachments')
		 && $this->db->tableHasField('#__forum_attachments', 'status'))
		{
			$query = "ALTER TABLE `#__forum_attachments` CHANGE `state` `status` int(11) NOT NULL default 0;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}