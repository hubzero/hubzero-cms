<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding courses gradebook table
 **/
class Migration20130320000000ComCourses extends Base
{
	public function up()
	{
		$query = "CREATE TABLE IF NOT EXISTS `#__courses_grade_book` (
						`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
						`user_id` int(11) NOT NULL,
						`score` decimal(5,2) NOT NULL DEFAULT '0.00',
						`scope` varchar(255) NOT NULL DEFAULT 'asset',
						`scope_id` int(11) NOT NULL DEFAULT '0',
						PRIMARY KEY (`id`)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

		$this->db->setQuery($query);
		$this->db->query();
	}

	public function down()
	{
		$query = "DROP TABLE IF EXISTS `#__courses_grade_book`;";

		$this->db->setQuery($query);
		$this->db->query();
	}
}