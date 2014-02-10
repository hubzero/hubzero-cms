<?php

use Hubzero\Content\Migration;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for ...
 **/
class Migration20140109024336ComGroups extends Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
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
			$db->setQuery($query);
			$db->query();
		}
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		// delete categories table
		if ($db->tableExists('#__xgroups_pages_checkout'))
		{
			$query = "DROP TABLE #__xgroups_pages_checkout;";
			$db->setQuery($query);
			$db->query();
		}
		
		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}
}