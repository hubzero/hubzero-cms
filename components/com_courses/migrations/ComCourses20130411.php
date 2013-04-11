<?php

class ComCourses20130411 extends Hubzero_Migration
{
	protected function up()
	{
		$query = "CREATE TABLE `jos_courses_grade_policies` (
						`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
						`alias` varchar(255) NOT NULL DEFAULT '',
						`description` mediumtext,
						`type` varchar(255) NOT NULL,
						`grade_criteria` mediumtext NOT NULL,
						`score_criteria` mediumtext NOT NULL,
						`badge_criteria` mediumtext NOT NULL,
						PRIMARY KEY (`id`)
					) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

				INSERT INTO `jos_courses_grade_policies` (`id`, `alias`, `description`, `type`, `grade_criteria`, `score_criteria`, `badge_criteria`)
				VALUES (1, 'passfail', 'An average exam score of 70% or greater is required to pass', 'passfail', '{\"select\":[{\"value\":\"IF(score >= 70, TRUE, FALSE) as passing\"}],\"from\":[],\"where\":[{\"field\":\"cgb.scope\",\"operator\":\"=\",\"value\":\"course\"},{\"field\":\"cgb.user_id\",\"operator\":\"=\",\"value\":\"[[user_id]]\"},{\"field\":\"cgb.scope_id\",\"operator\":\"=\",\"value\":\"[[course_id]]\"}],\"group\":[],\"having\":[]}', '{\"select\":[{\"value\":\"AVG(cgb.score) as average\"}],\"from\":[],\"where\":[{\"field\":\"ca.title\",\"operator\":\"LIKE\",\"value\":\"%exam%\"},{\"field\":\"ca.type\",\"operator\":\"=\",\"value\":\"exam\"},{\"field\":\"cgb.scope\",\"operator\":\"=\",\"value\":\"asset\"},{\"field\":\"cgb.user_id\",\"operator\":\"=\",\"value\":\"[[user_id]]\"},{\"field\":\"ca.course_id\",\"operator\":\"=\",\"value\":\"[[course_id]]\"}],\"group\":[],\"having\":[]}', 'pass');";

		$this->get('db')->exec($query);
	}

	protected function down()
	{
		$query = "DROP TABLE IF EXISTS `jos_courses_grade_policies`;";

		$this->get('db')->exec($query);
	}
}