<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for renaming previously added members fulltext index on givenName, middleName and surname fields
 **/
class Migration20140807143056ComMembers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		// drop orignal key and create new one
		if ($this->db->tableHasKey('#__xprofiles', 'jos_xprofiles_fullname_ftidx'))
		{
			$query = "ALTER TABLE `#__xprofiles` DROP INDEX jos_xprofiles_fullname_ftidx;";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "ALTER TABLE `#__xprofiles` ADD FULLTEXT ftidx_fullname (givenName, middleName, surname);";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		// drop orignal key and create new one
		if ($this->db->tableHasKey('#__xprofiles', 'ftidx_fullname'))
		{
			$query = "ALTER TABLE `#__xprofiles` DROP INDEX ftidx_fullname;";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "ALTER TABLE `#__xprofiles` ADD FULLTEXT jos_xprofiles_fullname_ftidx (givenName, middleName, surname);";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}