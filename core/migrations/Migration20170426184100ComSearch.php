<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script to remove unused search_queue table
 **/
class Migration20170426184100ComSearch extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		// Remove the newer table if it exists
		if ($this->db->tableExists('#__search_queue'))
		{
			$sql = "DROP TABLE #__search_queue;";
			$this->db->setQuery($sql);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
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
