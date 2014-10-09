<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for fixing some answers tables column types
 **/
class Migration20141009182840ComAnswers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__answers_log') && $this->db->tableHasField('#__answers_log', 'response_id'))
		{
			$info = $this->db->getTableColumns('#__answers_log', false);

			if ($info['response_id']->Type != "int(11) unsigned")
			{
				$query = "ALTER TABLE `#__answers_log` CHANGE COLUMN `response_id` `response_id` INT(11) UNSIGNED NOT NULL DEFAULT '0'";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__answers_questions'))
		{
			$info = $this->db->getTableColumns('#__answers_questions', false);

			if ($this->db->tableHasField('#__answers_questions', 'anonymous') && $info['anonymous']->Type != "tinyint(2) unsigned")
			{
				$query = "ALTER TABLE `#__answers_questions` CHANGE COLUMN `anonymous` `anonymous` TINYINT(2) UNSIGNED NOT NULL DEFAULT '0'";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__answers_questions', 'reward') && $info['reward']->Null != "NO")
			{
				$query = "ALTER TABLE `#__answers_questions` CHANGE COLUMN `reward` `reward` TINYINT(2) NOT NULL DEFAULT '0'";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__answers_questions_log') && $this->db->tableHasField('#__answers_questions_log', 'question_id'))
		{
			$info = $this->db->getTableColumns('#__answers_questions_log', false);

			if ($info['question_id']->Type != "int(11) unsigned")
			{
				$query = "ALTER TABLE `#__answers_questions_log` CHANGE COLUMN `question_id` `question_id` INT(11) UNSIGNED NOT NULL DEFAULT '0'";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__answers_responses'))
		{
			$info = $this->db->getTableColumns('#__answers_responses', false);

			if ($this->db->tableHasField('#__answers_responses', 'question_id') && $info['question_id']->Type != "int(11) unsigned")
			{
				$query = "ALTER TABLE `#__answers_responses` CHANGE COLUMN `question_id` `question_id` INT(11) UNSIGNED NOT NULL DEFAULT '0'";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__answers_responses', 'answer') && $info['answer']->Null != "NO")
			{
				$query = "ALTER TABLE `#__answers_responses` CHANGE COLUMN `answer` `answer` TEXT NOT NULL";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__answers_responses', 'anonymous') && $info['anonymous']->Type != "tinyint(2) unsigned")
			{
				$query = "ALTER TABLE `#__answers_responses` CHANGE COLUMN `anonymous` `anonymous` TINYINT(2) UNSIGNED NOT NULL DEFAULT '0'";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}