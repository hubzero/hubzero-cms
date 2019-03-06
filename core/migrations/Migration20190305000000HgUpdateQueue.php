<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for removing unused hg_update_queue table
 **/
class Migration20190305000000HgUpdateQueue extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('hg_update_queue'))
		{
			$query = "DROP TABLE IF EXISTS `hg_update_queue`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if (!$this->db->tableExists('hg_update_queue'))
		{
			$query = "CREATE TABLE `hg_update_queue` (
			  `action` enum('INSERT','UPDATE','DELETE') NOT NULL,
			  `table_name` varchar(50) NOT NULL,
			  `id` int(11) NOT NULL,
			  `other_id` int(11) DEFAULT NULL,
			  `note` text
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
