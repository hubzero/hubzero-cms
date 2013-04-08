<?php

class ComCourses20130320 extends Hubzero_Migration
{
	protected function up()
	{
		$query = "CREATE TABLE IF NOT EXISTS `jos_courses_grade_book` (
						`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
						`user_id` int(11) NOT NULL,
						`score` decimal(5,2) NOT NULL DEFAULT '0.00',
						`scope` varchar(255) NOT NULL DEFAULT 'asset',
						`scope_id` int(11) NOT NULL DEFAULT '0',
						PRIMARY KEY (`id`)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

		$this->get('db')->exec($query);
	}

	protected function down()
	{
		$query = "DROP TABLE IF EXISTS `jos_courses_grade_book`;";

		$this->get('db')->exec($query);
	}
}