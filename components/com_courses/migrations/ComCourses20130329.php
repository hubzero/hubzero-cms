<?php

class ComCourses20130329 extends Hubzero_Migration
{
	protected function up()
	{
		$query = "CREATE TABLE IF NOT EXISTS `jos_courses_member_notes` (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `scope` varchar(255) NOT NULL DEFAULT '',
		  `scope_id` int(11) NOT NULL DEFAULT '0',
		  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		  `created_by` int(11) NOT NULL DEFAULT '0',
		  `content` mediumtext NOT NULL,
		  `pos_x` int(11) NOT NULL DEFAULT '0',
		  `pos_y` int(11) NOT NULL DEFAULT '0',
		  `width` int(11) NOT NULL DEFAULT '0',
		  `height` int(11) NOT NULL DEFAULT '0',
		  `state` tinyint(2) NOT NULL DEFAULT '0',
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;
		
		INSERT INTO `jos_plugins` (`name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`)
		SELECT 'Courses - Notes','notes','courses',0,11,1,0,0,0,'0000-00-00 00:00:00',''
		FROM DUAL WHERE NOT EXISTS (SELECT `name` FROM `jos_plugins` WHERE `name` = 'Courses - Notes');";

		$this->get('db')->exec($query);
	}

	protected function down()
	{
		$query = "DROP TABLE IF EXISTS `jos_courses_member_notes`;
			DELETE FROM `jos_plugins` WHERE `element` = 'notes' AND `folder`='courses';";

		$this->get('db')->exec($query);
	}
}