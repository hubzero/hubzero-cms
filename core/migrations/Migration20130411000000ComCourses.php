<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding course grading policies
 **/
class Migration20130411000000ComCourses extends Base
{
	public function up()
	{
		if (!$this->db->tableExists('#__courses_grade_policies'))
		{
			$query = "CREATE TABLE `#__courses_grade_policies` (
							`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
							`alias` varchar(255) NOT NULL DEFAULT '',
							`description` mediumtext,
							`type` varchar(255) NOT NULL,
							`grade_criteria` mediumtext NOT NULL,
							`score_criteria` mediumtext NOT NULL,
							`badge_criteria` mediumtext NOT NULL,
							PRIMARY KEY (`id`)
						) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

					INSERT INTO `#__courses_grade_policies` (`id`, `alias`, `description`, `type`, `grade_criteria`, `score_criteria`, `badge_criteria`)
					VALUES (1, 'passfail', 'An average exam score of 70% or greater is required to pass', 'passfail', '{\"select\":[{\"value\":\"IF(score >= 70, TRUE, FALSE) as passing\"}],\"from\":[],\"where\":[{\"field\":\"cgb.scope\",\"operator\":\"=\",\"value\":\"course\"}],\"group\":[],\"having\":[]}', '{\"select\":[{\"value\":\"AVG(cgb.score) as average\"}],\"from\":[],\"where\":[{\"field\":\"ca.title\",\"operator\":\"LIKE\",\"value\":\"%exam%\"},{\"field\":\"ca.type\",\"operator\":\"=\",\"value\":\"exam\"},{\"field\":\"cgb.scope\",\"operator\":\"=\",\"value\":\"asset\"}],\"group\":[],\"having\":[]}', 'pass');";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	public function down()
	{
		$query = "DROP TABLE IF EXISTS `#__courses_grade_policies`;";

		$this->db->setQuery($query);
		$this->db->query();
	}
}