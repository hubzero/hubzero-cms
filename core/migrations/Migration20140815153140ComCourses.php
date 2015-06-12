<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding date columns to gradebook
 **/
class Migration20140815153140ComCourses extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__courses_grade_book')
			&& $this->db->tableHasField('#__courses_grade_book', 'override')
			&& !$this->db->tableHasField('#__courses_grade_book', 'score_recorded'))
		{
			$query = "ALTER TABLE `#__courses_grade_book` ADD `score_recorded` DATETIME NULL AFTER `override`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__courses_grade_book')
			&& $this->db->tableHasField('#__courses_grade_book', 'score_recorded')
			&& !$this->db->tableHasField('#__courses_grade_book', 'override_recorded'))
		{
			$query = "ALTER TABLE `#__courses_grade_book` ADD `override_recorded` DATETIME NULL AFTER `score_recorded`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// Get deployments
		$query = "SELECT cfd.`id`, `asset_id` FROM `#__courses_form_deployments` AS cfd, `#__courses_forms` AS cf WHERE `form_id` = cf.`id`";
		$this->db->setQuery($query);
		$deployments = $this->db->loadObjectList();

		if ($deployments && count($deployments) > 0)
		{
			foreach ($deployments as $deployment)
			{
				// Now set the score recorded dates for all existing forms
				$query = "SELECT member_id, finished, count(pfa.id)*100/count(pfr2.id) AS score
						  FROM `#__courses_form_respondents` pfr
						  LEFT JOIN `#__courses_form_latest_responses_view` pfr2 ON pfr2.respondent_id = pfr.id
						  LEFT JOIN `#__courses_form_questions` pfq ON pfq.id = pfr2.question_id
						  LEFT JOIN `#__courses_form_answers` pfa ON pfa.id = pfr2.answer_id AND pfa.correct
						  WHERE deployment_id = {$deployment->id}
						  GROUP BY member_id, started, finished, version
						  ORDER BY member_id ASC, score ASC, finished ASC";

				$this->db->setQuery($query);
				$results = $this->db->loadObjectList('member_id');

				if ($results && count($results) > 0)
				{
					foreach ($results as $result)
					{
						if (isset($result->finished))
						{
							$query = "UPDATE `#__courses_grade_book` SET `score_recorded` = '" . $result->finished . "'";
							$query .= " WHERE `member_id` = " . (int)$result->member_id . " AND `scope` = 'asset' AND `scope_id` = " . (int)$deployment->asset_id;
							$this->db->setQuery($query);
							$this->db->query();
						}
					}
				}
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__courses_grade_book') && $this->db->tableHasField('#__courses_grade_book', 'score_recorded'))
		{
			$query = "ALTER TABLE `#__courses_grade_book` DROP `score_recorded`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__courses_grade_book') && $this->db->tableHasField('#__courses_grade_book', 'override_recorded'))
		{
			$query = "ALTER TABLE `#__courses_grade_book` DROP `override_recorded`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}