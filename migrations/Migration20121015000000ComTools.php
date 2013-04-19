<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class Migration20121015000000ComTools extends Hubzero_Migration
{
	protected static function up($db)
	{
		$query = '';

		if (!$db->tableExists('#__venue') && !$db->tableExists('venue'))
		{
			$query .= "CREATE TABLE IF NOT EXISTS `#__venue` (
						`id` int(11) NOT NULL AUTO_INCREMENT,
						`venue` varchar(40),
						`network` varchar(40),
						PRIMARY KEY (`id`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8;\n";
		}
		if (!$db->tableExists('#__venue_countries') && !$db->tableExists('venue_countries'))
		{
			$query .= "CREATE TABLE IF NOT EXISTS `#__venue_countries` (
						`id` int(11) NOT NULL AUTO_INCREMENT,
						`countrySHORT` varchar(40),
						PRIMARY KEY (`id`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8;\n";
		}

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}

	protected static function down($db)
	{
		$query = '';

		if ($db->tableExists('#__venue'))
		{
			$query .= "DROP TABLE IF EXISTS `#__venue`;\n";
		}
		if ($db->tableExists('#__venue_countries'))
		{
			$query .= "DROP TABLE IF EXISTS `#__venue_countries`;\n";
		}

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}
}