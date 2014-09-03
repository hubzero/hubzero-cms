<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding indices and setting default field value
 **/
class Migration20131108095500ComAnswers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__answers_questions'))
		{
			if ($this->db->tableHasField('#__answers_questions', 'id'))
			{
				$query = "ALTER TABLE `#__answers_questions` CHANGE `id` `id` INT(11)  UNSIGNED  NOT NULL  AUTO_INCREMENT";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__answers_questions', 'subject'))
			{
				$query = "ALTER TABLE `#__answers_questions` CHANGE `subject` `subject` VARCHAR(250)  NOT NULL  DEFAULT ''";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__answers_questions', 'question'))
			{
				$query = "ALTER TABLE `#__answers_questions` CHANGE `question` `question` TEXT  NOT NULL";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__answers_questions', 'created_by'))
			{
				$query = "ALTER TABLE `#__answers_questions` CHANGE `created_by` `created_by` VARCHAR(50)  NOT NULL  DEFAULT ''";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__answers_questions', 'email'))
			{
				$query = "ALTER TABLE `#__answers_questions` CHANGE `email` `email` TINYINT(2)  UNSIGNED  NOT NULL  DEFAULT '0'";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__answers_questions', 'helpful'))
			{
				$query = "ALTER TABLE `#__answers_questions` CHANGE `helpful` `helpful` INT(11)  UNSIGNED  NOT NULL  DEFAULT '0'";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__answers_questions', 'idx_created_by'))
			{
				$query = "ALTER TABLE `#__answers_questions` ADD INDEX `idx_created_by` (`created_by`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__answers_questions', 'idx_state'))
			{
				$query = "ALTER TABLE `#__answers_questions` ADD INDEX `idx_state` (`state`);";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__answers_responses'))
		{
			if ($this->db->tableHasField('#__answers_responses', 'id'))
			{
				$query = "ALTER TABLE `#__answers_responses` CHANGE `id` `id` INT(11)  UNSIGNED  NOT NULL  AUTO_INCREMENT";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__answers_responses', 'qid'))
			{
				$query = "ALTER TABLE `#__answers_responses` CHANGE `qid` `qid` INT(11)  UNSIGNED  NOT NULL  DEFAULT '0'";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__answers_responses', 'created_by'))
			{
				$query = "ALTER TABLE `#__answers_responses` CHANGE `created_by` `created_by` VARCHAR(50)  NOT NULL  DEFAULT ''";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__answers_responses', 'state'))
			{
				$query = "ALTER TABLE `#__answers_responses` CHANGE `state` `state` TINYINT(3)  NOT NULL  DEFAULT '0'";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__answers_responses', 'nothelpful'))
			{
				$query = "ALTER TABLE `#__answers_responses` CHANGE `nothelpful` `nothelpful` INT(11)  UNSIGNED  NOT NULL  DEFAULT '0'";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__answers_responses', 'helpful'))
			{
				$query = "ALTER TABLE `#__answers_responses` CHANGE `helpful` `helpful` INT(11)  UNSIGNED  NOT NULL  DEFAULT '0'";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__answers_responses', 'idx_created_by'))
			{
				$query = "ALTER TABLE `#__answers_responses` ADD INDEX `idx_created_by` (`created_by`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__answers_responses', 'idx_state'))
			{
				$query = "ALTER TABLE `#__answers_responses` ADD INDEX `idx_state` (`state`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__answers_responses', 'idx_qid'))
			{
				$query = "ALTER TABLE `#__answers_responses` ADD INDEX `idx_qid` (`qid`);";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__answers_questions_log'))
		{
			if ($this->db->tableHasField('#__answers_questions_log', 'id'))
			{
				$query = "ALTER TABLE `#__answers_questions_log` CHANGE `id` `id` INT(11)  UNSIGNED  NOT NULL  AUTO_INCREMENT";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__answers_questions_log', 'voter'))
			{
				$query = "ALTER TABLE `#__answers_questions_log` CHANGE `voter` `voter` INT(11)  NOT NULL  DEFAULT '0'";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__answers_questions_log', 'ip'))
			{
				$query = "ALTER TABLE `#__answers_questions_log` CHANGE `ip` `ip` VARCHAR(15)  NOT NULL  DEFAULT ''";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__answers_questions_log', 'qid'))
			{
				$query = "ALTER TABLE `#__answers_questions_log` CHANGE `qid` `qid` INT(11)  UNSIGNED  NOT NULL  DEFAULT '0'";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__answers_questions_log', 'idx_voter'))
			{
				$query = "ALTER TABLE `#__answers_questions_log` ADD INDEX `idx_voter` (`voter`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__answers_questions_log', 'idx_qid'))
			{
				$query = "ALTER TABLE `#__answers_questions_log` ADD INDEX `idx_qid` (`qid`);";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__answers_log'))
		{
			if ($this->db->tableHasField('#__answers_log', 'id'))
			{
				$query = "ALTER TABLE `#__answers_log` CHANGE `id` `id` INT(11)  UNSIGNED  NOT NULL  AUTO_INCREMENT";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__answers_log', 'ip'))
			{
				$query = "ALTER TABLE `#__answers_log` CHANGE `ip` `ip` VARCHAR(15)  NOT NULL  DEFAULT ''";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__answers_log', 'rid'))
			{
				$query = "ALTER TABLE `#__answers_log` CHANGE `rid` `rid` INT(11)  UNSIGNED  NOT NULL  DEFAULT '0'";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__answers_log', 'helpful'))
			{
				$query = "ALTER TABLE `#__answers_log` CHANGE `helpful` `helpful` VARCHAR(10)  NOT NULL  DEFAULT ''";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__answers_log', 'idx_rid'))
			{
				$query = "ALTER TABLE `#__answers_log` ADD INDEX `idx_rid` (`rid`);";
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
			if ($this->db->tableHasKey('#__answers_questions', 'idx_created_by'))
			{
				$query = "ALTER TABLE `#__answers_questions` DROP INDEX `idx_created_by`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__answers_questions', 'idx_state'))
			{
				$query = "ALTER TABLE `#__answers_questions` DROP INDEX `idx_state`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__answers_responses'))
		{
			if ($this->db->tableHasKey('#__answers_responses', 'idx_created_by'))
			{
				$query = "ALTER TABLE `#__answers_responses` DROP INDEX `idx_created_by`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__answers_responses', 'idx_qid'))
			{
				$query = "ALTER TABLE `#__answers_responses` DROP INDEX `idx_qid`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__answers_responses', 'idx_state'))
			{
				$query = "ALTER TABLE `#__answers_responses` DROP INDEX `idx_state`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__answers_questions_log'))
		{
			if ($this->db->tableHasKey('#__answers_questions_log', 'idx_qid'))
			{
				$query = "ALTER TABLE `#__answers_questions_log` DROP INDEX `idx_qid`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__answers_questions_log', 'idx_voter'))
			{
				$query = "ALTER TABLE `#__answers_questions_log` DROP INDEX `idx_voter`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__answers_log'))
		{
			if ($this->db->tableHasKey('#__answers_log', 'idx_rid'))
			{
				$query = "ALTER TABLE `#__answers_log` DROP INDEX `idx_rid`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}