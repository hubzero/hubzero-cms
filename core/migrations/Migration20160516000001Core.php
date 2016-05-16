<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_HZEXEC_') or die();

/**
 * Migration script to add add a table for the notifications log
 **/
class Migration20160516000001Core extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__notifications'))
		{
			$query = "CREATE TABLE `#__notifications` (
					`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
					`scope` varchar(100) DEFAULT NULL,
					`scope_id` int(11) DEFAULT NULL,
					`notified` datetime DEFAULT NULL,
					`meta` varchar(255) DEFAULT NULL,
					PRIMARY KEY (`id`)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	public function down()
	{
		if ($this->db->tableExists('#__notifications'))
		{
			$query = "DROP TABLE `#__notifications`";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}

}