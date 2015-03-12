<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

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
		$query = "UPDATE `#__users` SET `name` = REPlACE(`name`, 0xc2ad, '')";
		$this->db->setQuery($query);
		$this->db->query();
	}
}