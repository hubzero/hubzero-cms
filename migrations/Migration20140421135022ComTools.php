<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script to add #__tool_version_zone table
 **/
class Migration20140421135022ComTools extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		if (!$db->tableExists('#__tool_version_zone'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__tool_version_zone` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `tool_version_id` int(11) NOT NULL,
			  `zone_id` int(11) NOT NULL,
			  `publish_up` datetime DEFAULT NULL,
			  `publish_down` datetime DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$db->setQuery($query);
			$db->query();
		}
	}
}
