<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for syncing homeDirectory between users and xprofiles
 **/
class Migration20160902134802ComUsers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__users') && $this->db->tableExists('#__xprofiles'))
		{
			$query = "UPDATE `#__xprofiles` AS x
						INNER JOIN `#__users` AS u ON x.`uidNumber`=u.`id`
						SET x.`homeDirectory` = u.`homeDirectory`
						WHERE x.`homeDirectory` != u.`homeDirectory`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
