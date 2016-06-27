<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script to add fulltext index to user name parts
 **/
class Migration20160627120117ComMembers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__users'))
		{
			if (!$this->db->tableHasKey('#__users', 'ftidx_fullname'))
			{
				$query = "ALTER TABLE `#__users` ADD FULLTEXT `ftidx_fullname` (`givenName`,`middleName`,`surname`);";
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
		if ($this->db->tableExists('#__users'))
		{
			if ($this->db->tableHasKey('#__users', 'ftidx_fullname'))
			{
				$query = "ALTER TABLE `#__users` DROP INDEX `ftidx_fullname`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
