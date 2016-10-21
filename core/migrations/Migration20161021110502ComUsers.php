<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for syncing loginShell and ftpShell between users and xprofiles
 **/
class Migration20161021110502ComUsers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__users') && $this->db->tableExists('#__xprofiles'))
		{
			$query = "UPDATE `#__users` AS u
						INNER JOIN `#__xprofiles` AS x ON x.`uidNumber`=u.`id`
						SET u.`loginShell` = x.`loginShell`
						WHERE u.`loginShell` != x.`loginShell`;";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "UPDATE `#__users` AS u
						INNER JOIN `#__xprofiles` AS x ON x.`uidNumber`=u.`id`
						SET u.`ftpShell` = x.`ftpShell`
						WHERE u.`ftpShell` != x.`ftpShell`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
