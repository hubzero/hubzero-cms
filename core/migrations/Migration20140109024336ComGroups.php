<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for tracking group pages checkouts
 **/
class Migration20140109024336ComGroups extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query = "CREATE TABLE IF NOT EXISTS `#__xgroups_pages_checkout` (
					`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
					`pageid` int(11) DEFAULT NULL,
					`userid` int(11) DEFAULT NULL,
					`when` datetime DEFAULT NULL,
				PRIMARY KEY (`id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		// delete categories table
		if ($this->db->tableExists('#__xgroups_pages_checkout'))
		{
			$query = "DROP TABLE #__xgroups_pages_checkout;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}