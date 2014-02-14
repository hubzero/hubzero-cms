<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding some needed indices to courses forms tables
 **/
class Migration20131206231821ComCourses extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__courses_form_deployments') && !$this->db->tableHasKey('#__courses_form_deployments', 'idx_crumb'))
		{
			$query = "CREATE UNIQUE INDEX idx_crumb ON #__courses_form_deployments(crumb)";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableExists('#__courses_form_responses') && !$this->db->tableHasKey('#__courses_form_responses', 'idx_respondent_id'))
		{
			$query = "CREATE INDEX idx_respondent_id ON #__courses_form_responses(respondent_id)";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableExists('#__courses_form_responses') && !$this->db->tableHasKey('#__courses_form_responses', 'idx_question_id'))
		{
			$query = "CREATE INDEX idx_question_id ON #__courses_form_responses(question_id)";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableExists('#__courses_form_responses') && !$this->db->tableHasKey('#__courses_form_responses', 'idx_answer_id'))
		{
			$query = "CREATE INDEX idx_answer_id ON #__courses_form_responses(answer_id)";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableExists('#__courses_form_answers') && !$this->db->tableHasKey('#__courses_form_answers', 'idx_question_id'))
		{
			$query = "CREATE INDEX idx_question_id ON #__courses_form_answers(question_id)";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableExists('#__courses_form_respondents') && !$this->db->tableHasKey('#__courses_form_respondents', 'idx_member_id'))
		{
			$query = "CREATE INDEX idx_member_id ON #__courses_form_respondents(member_id)";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableExists('#__courses_form_respondents') && !$this->db->tableHasKey('#__courses_form_respondents', 'idx_deployment_id'))
		{
			$query = "CREATE INDEX idx_deployment_id ON #__courses_form_respondents(deployment_id)";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__courses_form_deployments') && $this->db->tableHasKey('#__courses_form_deployments', 'idx_crumb'))
		{
			$query = "DROP INDEX idx_crumb ON #__courses_form_deployments(crumb)";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableExists('#__courses_form_responses') && $this->db->tableHasKey('#__courses_form_responses', 'idx_respondent_id'))
		{
			$query = "DROP INDEX idx_respondent_id ON #__courses_form_responses(respondent_id)";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableExists('#__courses_form_responses') && $this->db->tableHasKey('#__courses_form_responses', 'idx_question_id'))
		{
			$query = "DROP INDEX idx_question_id ON #__courses_form_responses(question_id)";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableExists('#__courses_form_responses') && $this->db->tableHasKey('#__courses_form_responses', 'idx_answer_id'))
		{
			$query = "DROP INDEX idx_answer_id ON #__courses_form_responses(answer_id)";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableExists('#__courses_form_answers') && $this->db->tableHasKey('#__courses_form_answers', 'idx_question_id'))
		{
			$query = "DROP INDEX idx_question_id ON #__courses_form_answers(question_id)";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableExists('#__courses_form_respondents') && $this->db->tableHasKey('#__courses_form_respondents', 'idx_member_id'))
		{
			$query = "DROP INDEX idx_member_id ON #__courses_form_respondents(member_id)";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableExists('#__courses_form_respondents') && $this->db->tableHasKey('#__courses_form_respondents', 'idx_deployment_id'))
		{
			$query = "DROP INDEX idx_deployment_id ON #__courses_form_respondents(deployment_id)";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}