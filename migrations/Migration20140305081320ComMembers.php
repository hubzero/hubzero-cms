<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for dropping profile tags table
 **/
class Migration20140305081320ComMembers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query = "DROP TABLE IF EXISTS `#__xprofiles_tags`;";
		$this->db->setQuery($query);
		$this->db->query();
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$query = "CREATE TABLE `#__xprofiles_tags` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `uidNumber` int(11) DEFAULT NULL,
		  `tagid` int(11) DEFAULT NULL,
		  `taggerid` int(11) DEFAULT '0',
		  `taggedon` datetime DEFAULT '0000-00-00 00:00:00',
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$this->db->setQuery($query);
		$this->db->query();
	}
}