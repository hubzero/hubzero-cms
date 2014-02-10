<?php

use Hubzero\Content\Migration;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding some needed indices to courses forms tables
 **/
class Migration20131206231821ComCourses extends Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		if ($db->tableExists('#__courses_form_deployments') && !$db->tableHasKey('#__courses_form_deployments', 'idx_crumb'))
		{
			$query = "CREATE UNIQUE INDEX idx_crumb ON #__courses_form_deployments(crumb)";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableExists('#__courses_form_responses') && !$db->tableHasKey('#__courses_form_responses', 'idx_respondent_id'))
		{
			$query = "CREATE INDEX idx_respondent_id ON #__courses_form_responses(respondent_id)";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableExists('#__courses_form_responses') && !$db->tableHasKey('#__courses_form_responses', 'idx_question_id'))
		{
			$query = "CREATE INDEX idx_question_id ON #__courses_form_responses(question_id)";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableExists('#__courses_form_responses') && !$db->tableHasKey('#__courses_form_responses', 'idx_answer_id'))
		{
			$query = "CREATE INDEX idx_answer_id ON #__courses_form_responses(answer_id)";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableExists('#__courses_form_answers') && !$db->tableHasKey('#__courses_form_answers', 'idx_question_id'))
		{
			$query = "CREATE INDEX idx_question_id ON #__courses_form_answers(question_id)";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableExists('#__courses_form_respondents') && !$db->tableHasKey('#__courses_form_respondents', 'idx_member_id'))
		{
			$query = "CREATE INDEX idx_member_id ON #__courses_form_respondents(member_id)";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableExists('#__courses_form_respondents') && !$db->tableHasKey('#__courses_form_respondents', 'idx_deployment_id'))
		{
			$query = "CREATE INDEX idx_deployment_id ON #__courses_form_respondents(deployment_id)";
			$db->setQuery($query);
			$db->query();
		}
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		if ($db->tableExists('#__courses_form_deployments') && $db->tableHasKey('#__courses_form_deployments', 'idx_crumb'))
		{
			$query = "DROP INDEX idx_crumb ON #__courses_form_deployments(crumb)";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableExists('#__courses_form_responses') && $db->tableHasKey('#__courses_form_responses', 'idx_respondent_id'))
		{
			$query = "DROP INDEX idx_respondent_id ON #__courses_form_responses(respondent_id)";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableExists('#__courses_form_responses') && $db->tableHasKey('#__courses_form_responses', 'idx_question_id'))
		{
			$query = "DROP INDEX idx_question_id ON #__courses_form_responses(question_id)";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableExists('#__courses_form_responses') && $db->tableHasKey('#__courses_form_responses', 'idx_answer_id'))
		{
			$query = "DROP INDEX idx_answer_id ON #__courses_form_responses(answer_id)";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableExists('#__courses_form_answers') && $db->tableHasKey('#__courses_form_answers', 'idx_question_id'))
		{
			$query = "DROP INDEX idx_question_id ON #__courses_form_answers(question_id)";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableExists('#__courses_form_respondents') && $db->tableHasKey('#__courses_form_respondents', 'idx_member_id'))
		{
			$query = "DROP INDEX idx_member_id ON #__courses_form_respondents(member_id)";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableExists('#__courses_form_respondents') && $db->tableHasKey('#__courses_form_respondents', 'idx_deployment_id'))
		{
			$query = "DROP INDEX idx_deployment_id ON #__courses_form_respondents(deployment_id)";
			$db->setQuery($query);
			$db->query();
		}
	}
}