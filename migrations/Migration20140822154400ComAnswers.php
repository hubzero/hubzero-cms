<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for renaming fulltext index on #__answers_questions
 **/
class Migration20140822154400ComAnswers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__answers_questions'))
		{
			if ($this->db->tableHasKey('#__answers_questions', 'jos_answers_questions_question_subject_ftidx'))
			{
				$query = "ALTER TABLE `#__answers_questions` DROP INDEX `jos_answers_questions_question_subject_ftidx`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__answers_questions', 'ftidx_question_subject'))
			{
				$query = "ALTER TABLE `#__answers_questions` ADD FULLTEXT `ftidx_question_subject` (`question`, `subject`);";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__answers_questions'))
		{
			if ($this->db->tableHasKey('#__answers_questions', 'ftidx_question_subject'))
			{
				$query = "ALTER TABLE `#__answers_questions` DROP INDEX `ftidx_question_subject`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__answers_questions', 'jos_answers_questions_question_subject_ftidx'))
			{
				$query = "ALTER TABLE `#__answers_questions` ADD FULLTEXT `jos_answers_questions_question_subject_ftidx` (`question`, `subject`);";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}