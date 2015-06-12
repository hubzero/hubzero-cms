<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding asset subtype to courses assets
 **/
class Migration20130423001442ComCourses extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query = "";

		if (!$this->db->tableHasField('#__courses_assets', 'subtype'))
		{
			$query .= "ALTER TABLE `#__courses_assets` ADD `subtype` VARCHAR(255)  NOT NULL  DEFAULT 'file'  AFTER `type`;";

			$query .= "UPDATE `#__courses_assets` SET `subtype` = `type`;
						UPDATE `#__courses_assets` SET `subtype` = 'quiz' WHERE `type` = 'exam' AND `title` LIKE '%quiz%';
						UPDATE `#__courses_assets` SET `subtype` = 'homework' WHERE `type` = 'exam' AND `title` LIKE '%homework%';
						UPDATE `#__courses_assets` SET `subtype` = 'embedded' WHERE `type` = 'video' AND `content` IS NOT NULL AND `content` != '';

						UPDATE `#__courses_assets` SET `type` = 'form' WHERE `type` = 'exam';
						UPDATE `#__courses_assets` SET `type` = 'text' WHERE `type` = 'note';
						UPDATE `#__courses_assets` SET `type` = 'text' WHERE `type` = 'wiki';
						UPDATE `#__courses_assets` SET `type` = 'url' WHERE `type` = 'link';";

			$query .= 'UPDATE `#__courses_grade_policies`
						SET
						`description` = \'Scores are based on the average of all exams.  An average exam score of 70% or greater is required to pass.\',
						`grade_criteria` = \'{"select":[{"value":"IF(score >= 70, TRUE, FALSE) as passing"}],"from":[],"where":[{"field":"cgb.scope","operator":"=","value":"course"}],"group":[],"having":[]}\',
						`score_criteria` = \'{"select":[{"value":"AVG(cgb.score) as average"}],"from":[],"where":[{"field":"ca.subtype","operator":"=","value":"exam"},{"field":"cgb.scope","operator":"=","value":"asset"}],"group":[],"having":[]}\'
						WHERE `id` = 1;';

		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}