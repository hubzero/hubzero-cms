<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for installing course reviews table
 **/
class Migration20170901000000PlgCoursesReviews extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__courses_reviews'))
		{
			$query = "CREATE TABLE `#__courses_reviews` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `course_id` int(11) NOT NULL DEFAULT '0',
			  `offering_id` int(11) NOT NULL DEFAULT '0',
			  `rating` decimal(2,1) NOT NULL DEFAULT '0.0',
			  `content` text NOT NULL,
			  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			  `created_by` int(11) NOT NULL DEFAULT '0',
			  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			  `modified_by` int(11) NOT NULL DEFAULT '0',
			  `anonymous` tinyint(2) NOT NULL DEFAULT '0',
			  `parent` int(11) NOT NULL DEFAULT '0',
			  `access` tinyint(2) NOT NULL DEFAULT '0',
			  `state` tinyint(2) NOT NULL DEFAULT '0',
			  `positive` int(11) NOT NULL DEFAULT '0',
			  `negative` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__courses_reviews'))
		{
			$query = "DROP TABLE IF EXISTS `#__courses_reviews`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
