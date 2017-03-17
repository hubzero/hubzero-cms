<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for replacing the indexqueue table
 **/
class Migration20161209133804ComSearch extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		// Remove the old table if it exists
		if ($this->db->tableExists('#__search_indexqueue'))
		{
			$sql = "DROP TABLE #__search_indexqueue;";
			$this->db->setQuery($sql);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__search_queue'))
		{
			// Build the new table
			$sql1 = "CREATE TABLE `#__search_queue` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`type` varchar(20) NOT NULL DEFAULT '',
				`type_id` int(11) NOT NULL,
				`status` int(11) NOT NULL DEFAULT '0',
				`action` varchar(20) NOT NULL,
				`created_by` int(11) DEFAULT NULL,
				`created` timestamp NULL DEFAULT NULL,
				`modified` timestamp NULL DEFAULT NULL,
				PRIMARY KEY (`id`)
				) ENGINE=MyISAM DEFAULT CHARSET=latin1;";

			$this->db->setQuery($sql1);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		// Remove the newer table if it exists
		if ($this->db->tableExists('#__search_queue'))
		{
			$sql = "DROP TABLE #__search_queue;";
			$this->db->setQuery($sql);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__search_indexqueue'))
		{
			// Build the older table
			$sql1 = "CREATE TABLE `#__search_indexqueue` (
 				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
 				`hubtype` varchar(12) NOT NULL DEFAULT '',
				`action` varchar(12) DEFAULT NULL,
				`start` int(11) NOT NULL DEFAULT '0',
				`lock` tinyint(1) NOT NULL DEFAULT '0',
				`complete` tinyint(1) NOT NULL DEFAULT '0',
				`created` timestamp NULL DEFAULT NULL,
				`created_by` int(11) DEFAULT NULL,
				`modified` timestamp NULL DEFAULT NULL,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;";

			$this->db->setQuery($sql1);
			$this->db->query();
		}
	}
}
