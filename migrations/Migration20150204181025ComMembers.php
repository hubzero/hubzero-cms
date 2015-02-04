<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for making sure usernames are all lowercase
 **/
class Migration20150204181025ComMembers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__xprofiles'))
		{
			if ($this->db->tableHasField('#__xprofiles', 'username'))
			{
				$query = "UPDATE `#__xprofiles` SET `username` = LOWER(`username`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__xprofiles', 'homeDirectory'))
			{
				$query = "UPDATE `#__xprofiles` SET `homeDirectory` = LOWER(`homeDirectory`)";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__users') && $this->db->tableHasField('#__users', 'username'))
		{
			$query = "UPDATE `#__users` SET `username` = LOWER(`username`)";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__event_registration') && $this->db->tableHasField('#__event_registration', 'username'))
		{
			$query = "UPDATE `#__event_registration` SET `username` = LOWER(`username`)";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__support_acl_aros') && $this->db->tableHasField('#__support_acl_aros', 'alias'))
		{
			$query = "UPDATE `#__support_acl_aros` SET `alias` = LOWER(`alias`)";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__support_tickets') && $this->db->tableHasField('#__support_tickets', 'login'))
		{
			$query = "UPDATE `#__support_tickets` SET `login` = LOWER(`login`)";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__tool') && $this->db->tableHasField('#__tool', 'team'))
		{
			$query = "UPDATE `#__tool` SET `team` = LOWER(`team`)";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__tool') && $this->db->tableHasField('#__tool', 'registered_by'))
		{
			$query = "UPDATE `#__tool` SET `registered_by` = LOWER(`registered_by`)";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__tool_version') && $this->db->tableHasField('#__tool_version', 'released_by'))
		{
			$query = "UPDATE `#__tool_version` SET `released_by` = LOWER(`released_by`)";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}