<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for removing bogus characters from names
 **/
class Migration20150306115852ComMembers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__xprofiles'))
		{
			$query = "UPDATE `#__xprofiles` SET `name` = REPlACE(`name`, 0xc2ad, '')";
			$this->db->setQuery($query);
			$this->db->query();
			$query = "UPDATE `#__xprofiles` SET `givenName` = REPlACE(`givenName`, 0xc2ad, '')";
			$this->db->setQuery($query);
			$this->db->query();
			$query = "UPDATE `#__xprofiles` SET `middleName` = REPlACE(`middleName`, 0xc2ad, '')";
			$this->db->setQuery($query);
			$this->db->query();
			$query = "UPDATE `#__xprofiles` SET `surname` = REPlACE(`surname`, 0xc2ad, '')";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableExists('#__users'))
		{
			$query = "UPDATE `#__users` SET `name` = REPlACE(`name`, 0xc2ad, '')";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}