<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for grade policy changes
 **/
class Migration20130506233657ComCourses extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query = "";

		if ($this->db->tableHasField('#__courses_grade_policies', 'score_criteria'))
		{
			// If the table is of the 'old' style, just get rid of it
			$query .= "DROP TABLE `#__courses_grade_policies`;\n";

			// Now create the new one
			$query .= "CREATE TABLE `#__courses_grade_policies` (
						`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
						`description` mediumtext,
						`threshold` decimal(3,2) DEFAULT NULL,
						`exam_weight` decimal(3,2) DEFAULT NULL,
						`quiz_weight` decimal(3,2) DEFAULT NULL,
						`homework_weight` decimal(3,2) DEFAULT NULL,
						PRIMARY KEY (`id`)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8;\n";

			// Insert default row
			$query .= "INSERT INTO `#__courses_grade_policies` (`id`, `description`, `threshold`, `exam_weight`, `quiz_weight`, `homework_weight`)
						VALUES (1, 'An average exam score of 70% or greater is required to pass the class.  Quizzes and homeworks do not count toward the final score.', 0.70, 1.00, 0.00, 0.00);";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}