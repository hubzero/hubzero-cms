<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding the blacklist table
 **/
class Migration20160805180813ComSearch extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__search_blacklist'))
		{
			$query = "CREATE TABLE `#__search_blacklist` (
			`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`scope` varchar(11) NOT NULL DEFAULT '',
			`scope_id` int(11) NOT NULL,
			`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
			`created_by` int(11) DEFAULT NULL,
			PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__search_blacklist'))
		{
			$query = "DROP TABLE #__search_blacklist";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
