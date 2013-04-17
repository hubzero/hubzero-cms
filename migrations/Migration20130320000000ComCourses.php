<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class Migration20130320000000ComCourses extends Hubzero_Migration
{
	protected static function up(&$db)
	{
		$query = "CREATE TABLE IF NOT EXISTS `#__courses_grade_book` (
						`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
						`user_id` int(11) NOT NULL,
						`score` decimal(5,2) NOT NULL DEFAULT '0.00',
						`scope` varchar(255) NOT NULL DEFAULT 'asset',
						`scope_id` int(11) NOT NULL DEFAULT '0',
						PRIMARY KEY (`id`)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

		$db->setQuery($query);
		$db->query();
	}

	protected static function down(&$db)
	{
		$query = "DROP TABLE IF EXISTS `#__courses_grade_book`;";

		$db->setQuery($query);
		$db->query();
	}
}