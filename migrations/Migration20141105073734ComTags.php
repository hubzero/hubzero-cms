<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for dropping unused #__tags_group table
 **/
class Migration20141105073734ComTags extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__tags_group'))
		{
			$query = "DROP TABLE IF EXISTS `#__tags_group`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Up
	 **/
	public function down()
	{
		if (!$this->db->tableExists('#__tags_group'))
		{
			$query = "CREATE TABLE `#__tags_group` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `groupid` int(11) unsigned NOT NULL DEFAULT '0',
			  `tagid` int(11) unsigned NOT NULL DEFAULT '0',
			  `priority` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_tagid` (`tagid`),
			  KEY `idx_groupid` (`groupid`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}