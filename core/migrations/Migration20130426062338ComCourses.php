<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for updating grading policies
 **/
class Migration20130426062338ComCourses extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableHasField('#__courses_grade_policies', 'score_criteria'))
		{
			$query = "DELETE FROM `#__courses_grade_policies` WHERE `id` = 1;";
			$query .= "INSERT INTO `#__courses_grade_policies` (`id`, `alias`, `description`, `type`, `grade_criteria`, `score_criteria`, `badge_criteria`)
					VALUES (1, 'passfail', 'Scores are based on the average of all exams.  An average exam score of 70% or greater is required to pass.', 'passfail', 'SELECT IF(score >= 70, TRUE, FALSE) as passing, cgb.user_id\nFROM #__courses_grade_book AS cgb\n[[::section_id::LEFT JOIN #__courses_members AS cm ON cgb.user_id = cm.user_id]]\nWHERE scope = \'course\'\n[[:scope_id:AND scope_id = \'{{var}}\']]\n[[:section_id:AND cm.section_id = \'{{var}}\']]\n[[:user_id:AND cgb.user_id = \'{{var}}\']]\n[[::section_id::AND cm.student = 1]]', 'SELECT user_id, AVG(cgb.score) as average[[:scope:, \'{{var}}\' as scope]][[:scope:, {{var}}_id as scope_id]]\nFROM #__courses_grade_book AS cgb\nLEFT JOIN #__courses_assets AS ca ON cgb.scope_id = ca.id\n[[::unit::LEFT JOIN #__courses_asset_associations AS caa ON ca.id = caa.asset_id]]\n[[::unit::LEFT JOIN #__courses_asset_groups AS cag ON caa.scope_id = cag.id]]\nWHERE ca.subtype = \'exam\'\nAND ca.state = 1\nAND cgb.scope = \'asset\'\n[[:user_id:AND cgb.user_id = \'{{var}}\']]\n[[:course_id:AND ca.course_id = \'{{var}}\']]\nGROUP BY user_id[[::unit::, cag.unit_id]]', 'pass');";

			$query .= "INSERT INTO `#__courses_grade_policies` (`alias`, `description`, `type`, `grade_criteria`, `score_criteria`, `badge_criteria`)
					VALUES ('minminusone', 'Scores are based on the average of all exams.  A minimum exam score of 70% or greater on all exams, less the lowest one, is required to pass.', 'passfail', 'SELECT if(min(score)>70, TRUE, FALSE) AS passing, user_id\nFROM (\n	SELECT cgb.user_id as user_id, score, section_id, student,\n		@num := if(@user_id = cgb.user_id, @num + 1, 1) AS row_number,\n		@user_id := cgb.user_id AS placeholder\n	FROM #__courses_grade_book cgb\n	LEFT JOIN #__courses_assets ca ON cgb.scope_id = ca.id\n	[[::section_id::LEFT JOIN #__courses_members AS cm ON cgb.user_id = cm.user_id]]\n	WHERE scope = \'asset\'\n	AND score IS NOT NULL\n	AND ca.subtype = \'exam\'\n	AND ca.state = 1\n	[[:user_id:AND cgb.user_id = \'{{var}}\']]\n	[[:section_id:HAVING cm.section_id = \'{{var}}\']]\n	[[::section_id::AND cm.student = \'1\']]\n	ORDER BY cgb.user_id asc, score asc ) AS sub\nWHERE sub.row_number != 1\nGROUP BY user_id', 'SELECT user_id, AVG(cgb.score) as average[[:scope:, \'{{var}}\' as scope]][[:scope:, {{var}}_id as scope_id]]\nFROM #__courses_grade_book AS cgb\nLEFT JOIN #__courses_assets AS ca ON cgb.scope_id = ca.id\n[[::unit::LEFT JOIN #__courses_asset_associations AS caa ON ca.id = caa.asset_id]]\n[[::unit::LEFT JOIN #__courses_asset_groups AS cag ON caa.scope_id = cag.id]]\nWHERE ca.subtype = \'exam\'\nAND ca.state = 1\nAND cgb.scope = \'asset\'\n[[:user_id:AND cgb.user_id = \'{{var}}\']]\n[[:course_id:AND ca.course_id = \'{{var}}\']]\nGROUP BY user_id[[::unit::, cag.unit_id]]', 'pass');";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
