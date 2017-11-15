<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for moving registerDate data from #__xprofiles to #__users
 **/
class Migration20170405140417ComMembers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__xprofiles')
		 && $this->db->tableExists('#__users')
		 && $this->db->tableHasField('#__xprofiles', 'registerDate')
		 && $this->db->tableHasField('#__users', 'registerDate'))
		{
			$query = "UPDATE `#__users` AS u
					INNER JOIN `#__xprofiles` AS p ON p.`uidNumber`=u.`id`
					SET u.registerDate=p.registerDate WHERE u.registerDate = '0000-00-00 00:00:00' OR u.registerDate IS NULL;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
