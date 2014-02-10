<?php

use Hubzero\Content\Migration;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding params field to asset groups
 **/
class Migration20140130105700ComAnswers extends Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		if ($db->tableHasField('#__answers_questions', 'created_by'))
		{
			$query = "SELECT id, created_by FROM `#__answers_questions`";
			$db->setQuery($query);
			$results = $db->loadObjectList();

			if ($results && count($results) > 0)
			{
				foreach ($results as $r)
				{
					$query  = "SELECT `id` FROM `#__users` WHERE `username` = '{$r->created_by}'";
					$db->setQuery($query);
					$id = $db->loadResult();

					$query = "UPDATE `#__answers_questions` SET `created_by` = '{$id}' WHERE `id` = '{$r->id}'";
					$db->setQuery($query);
					$db->query();
				}
			}

			$query = "ALTER TABLE `#__answers_questions` CHANGE `created_by` `created_by` int(11) NOT NULL DEFAULT '0';";
			$db->setQuery($query);
			$db->query();
		}

		if ($db->tableHasField('#__answers_responses', 'created_by'))
		{
			$query = "SELECT id, created_by FROM `#__answers_responses`";
			$db->setQuery($query);
			$results = $db->loadObjectList();

			if ($results && count($results) > 0)
			{
				foreach ($results as $r)
				{
					$query  = "SELECT `id` FROM `#__users` WHERE `username` = '{$r->created_by}'";
					$db->setQuery($query);
					$id = $db->loadResult();

					$query = "UPDATE `#__answers_responses` SET `created_by` = '{$id}' WHERE `id` = '{$r->id}'";
					$db->setQuery($query);
					$db->query();
				}
			}

			$query = "ALTER TABLE `#__answers_responses` CHANGE `created_by` `created_by` int(11) NOT NULL DEFAULT '0';";
			$db->setQuery($query);
			$db->query();
		}

		if ($db->tableHasField('#__answers_responses', 'qid'))
		{
			$query = "ALTER TABLE `#__answers_responses` CHANGE `qid` `question_id` int(11) NOT NULL DEFAULT '0';";
			$db->setQuery($query);
			$db->query();
		}

		if ($db->tableHasField('#__answers_questions_log', 'qid'))
		{
			$query = "ALTER TABLE `#__answers_questions_log` CHANGE `qid` `question_id` int(11) NOT NULL DEFAULT '0';";
			$db->setQuery($query);
			$db->query();
		}

		if ($db->tableHasField('#__answers_log', 'rid'))
		{
			$query = "ALTER TABLE `#__answers_log` CHANGE `rid` `response_id` int(11) NOT NULL DEFAULT '0';";
			$db->setQuery($query);
			$db->query();
		}
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		if ($db->tableHasField('#__answers_questions', 'created_by'))
		{
			$query = "SELECT id, created_by FROM `#__answers_questions`";
			$db->setQuery($query);
			$results = $db->loadObjectList();

			if ($results && count($results) > 0)
			{
				foreach ($results as $r)
				{
					$query  = "SELECT `username` FROM `#__users` WHERE `id` = '{$r->created_by}'";
					$db->setQuery($query);
					$id = $db->loadResult();

					$query = "UPDATE `#__answers_questions` SET `created_by` = '{$id}' WHERE `id` = '{$r->id}'";
					$db->setQuery($query);
					$db->query();
				}
			}

			$query = "ALTER TABLE `#__answers_questions` CHANGE `created_by` `created_by` varchar(50) DEAULT NULL;";
			$db->setQuery($query);
			$db->query();
		}

		if ($db->tableHasField('#__answers_responses', 'created_by'))
		{
			$query = "SELECT id, created_by FROM `#__answers_responses`";
			$db->setQuery($query);
			$results = $db->loadObjectList();

			if ($results && count($results) > 0)
			{
				foreach ($results as $r)
				{
					$query  = "SELECT `username` FROM `#__users` WHERE `id` = '{$r->created_by}'";
					$db->setQuery($query);
					$id = $db->loadResult();

					$query = "UPDATE `#__answers_responses` SET `created_by` = '{$id}' WHERE `id` = '{$r->id}'";
					$db->setQuery($query);
					$db->query();
				}
			}

			$query = "ALTER TABLE `#__answers_responses` CHANGE `created_by` `created_by` varchar(50) DEAULT NULL;";
			$db->setQuery($query);
			$db->query();
		}

		if ($db->tableHasField('#__answers_responses', 'question_id'))
		{
			$query = "ALTER TABLE `#__answers_responses` CHANGE `question_id` `qid` int(11) NOT NULL DEFAULT '0';";
			$db->setQuery($query);
			$db->query();
		}

		if ($db->tableHasField('#__answers_questions_log', 'question_id'))
		{
			$query = "ALTER TABLE `#__answers_questions_log` CHANGE `question_id` `qid` int(11) NOT NULL DEFAULT '0';";
			$db->setQuery($query);
			$db->query();
		}

		if ($db->tableHasField('#__answers_log', 'response_id'))
		{
			$query = "ALTER TABLE `#__answers_log` CHANGE `response_id` `rid` int(11) NOT NULL DEFAULT '0';";
			$db->setQuery($query);
			$db->query();
		}
	}
}