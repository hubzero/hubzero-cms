<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for renaming tables and fields to conform to naming conventions
 **/
class Migration20160425154200ComPoll extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__polls'))
		{
			if ($this->db->tableHasField('#__polls', 'published')
			 && !$this->db->tableHasField('#__polls', 'state'))
			{
				$query = "ALTER TABLE `#__polls` CHANGE `published` `state` tinyint(1) default 0;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__poll_data'))
		{
			$query = "RENAME TABLE `#__poll_data` TO `#__poll_options`";
			$this->db->setQuery($query);
			$this->db->query();

			if ($this->db->tableHasField('#__poll_options', 'pollid')
			 && !$this->db->tableHasField('#__poll_options', 'poll_id'))
			{
				$query = "ALTER TABLE `#__poll_options` CHANGE `pollid` `poll_id` int(11) default 0;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__poll_date'))
		{
			$query = "RENAME TABLE `#__poll_date` TO `#__poll_dates`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__poll_menu'))
		{
			$query = "RENAME TABLE `#__poll_menu` TO `#__poll_menus`";
			$this->db->setQuery($query);
			$this->db->query();

			if ($this->db->tableHasField('#__poll_menus', 'pollid')
			 && !$this->db->tableHasField('#__poll_menus', 'poll_id'))
			{
				$query = "ALTER TABLE `#__poll_menus` CHANGE `pollid` `poll_id` int(11) default 0;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__poll_menus', 'menuid')
			 && !$this->db->tableHasField('#__poll_menus', 'menu_id'))
			{
				$query = "ALTER TABLE `#__poll_menus` CHANGE `menuid` `menu_id` int(11) default 0;";
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
		if ($this->db->tableExists('#__polls'))
		{
			if ($this->db->tableHasField('#__polls', 'state')
			 && !$this->db->tableHasField('#__polls', 'published'))
			{
				$query = "ALTER TABLE `#__polls` CHANGE `state` `published` tinyint(1) default 0;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__poll_options'))
		{
			$query = "RENAME TABLE `#__poll_options` TO `#__poll_data`";
			$this->db->setQuery($query);
			$this->db->query();

			if ($this->db->tableHasField('#__poll_data', 'poll_id')
			 && !$this->db->tableHasField('#__poll_data', 'pollid'))
			{
				$query = "ALTER TABLE `#__poll_options` CHANGE `poll_id` `pollid` int(4) default 0;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__poll_dates'))
		{
			$query = "RENAME TABLE `#__poll_dates` TO `#__poll_date`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__poll_menus'))
		{
			$query = "RENAME TABLE `#__poll_menus` TO `#__poll_menu`";
			$this->db->setQuery($query);
			$this->db->query();

			if ($this->db->tableHasField('#__poll_menu', 'poll_id')
			 && !$this->db->tableHasField('#__poll_menu', 'pollid'))
			{
				$query = "ALTER TABLE `#__poll_menu` CHANGE `poll_id` `pollid` int(11) default 0;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__poll_menu', 'menu_id')
			 && !$this->db->tableHasField('#__poll_menu', 'menuid'))
			{
				$query = "ALTER TABLE `#__poll_menu` CHANGE `menu_id` `menuid` int(11) default 0;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}