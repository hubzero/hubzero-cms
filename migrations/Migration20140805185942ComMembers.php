<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding fulltext index to xprofiles giveName, middleName, and surname fields.
 **/
class Migration20140805185942ComMembers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableHasKey('#__xprofiles', 'jos_xprofiles_fullname_ftidx'))
		{
			$query = "ALTER TABLE `#__xprofiles` ADD FULLTEXT jos_xprofiles_fullname_ftidx (givenName, middleName, surname);";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableHasKey('#__xprofiles', 'jos_xprofiles_fullname_ftidx'))
		{
			$query = "ALTER TABLE `#__xprofiles` DROP INDEX jos_xprofiles_fullname_ftidx;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}