<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding table for tracking recently visited groups
 **/
class Migration20160419144221ComGroups extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__xgroups_recents'))
		{
			$query = "CREATE TABLE `#__xgroups_recents` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
				  `group_id` int(11) unsigned NOT NULL DEFAULT '0',
				  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				  PRIMARY KEY (`id`),
				  KEY `idx_user_id` (`user_id`),
				  KEY `idx_group_id` (`group_id`)
				) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__xgroups_recents'))
		{
			$query = "DROP TABLE `#__xgroups_recents`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
