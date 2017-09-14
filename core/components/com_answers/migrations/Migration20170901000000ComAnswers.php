<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for installing answers tables
 **/
class Migration20170901000000ComAnswers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__answers_questions'))
		{
			$query = "CREATE TABLE `#__answers_questions` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `subject` varchar(250) NOT NULL DEFAULT '',
			  `question` text NOT NULL,
			  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			  `created_by` int(11) NOT NULL DEFAULT '0',
			  `state` tinyint(3) NOT NULL DEFAULT '0',
			  `anonymous` tinyint(2) unsigned NOT NULL DEFAULT '0',
			  `email` tinyint(2) unsigned NOT NULL DEFAULT '0',
			  `helpful` int(11) unsigned NOT NULL DEFAULT '0',
			  `reward` tinyint(2) NOT NULL DEFAULT '0',
			  `nothelpful` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_state` (`state`),
			  KEY `idx_created_by` (`created_by`),
			  FULLTEXT KEY `ftidx_question` (`question`),
			  FULLTEXT KEY `ftidx_subject` (`subject`),
			  FULLTEXT KEY `ftidx_question_subject` (`question`,`subject`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__answers_responses'))
		{
			$query = "CREATE TABLE `#__answers_responses` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `question_id` int(11) unsigned NOT NULL DEFAULT '0',
			  `answer` text NOT NULL,
			  `created_by` int(11) NOT NULL DEFAULT '0',
			  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			  `helpful` int(11) unsigned NOT NULL DEFAULT '0',
			  `nothelpful` int(11) unsigned NOT NULL DEFAULT '0',
			  `state` tinyint(3) NOT NULL DEFAULT '0',
			  `anonymous` tinyint(2) unsigned NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_qid` (`question_id`),
			  KEY `idx_state` (`state`),
			  KEY `idx_created_by` (`created_by`),
			  FULLTEXT KEY `ftidx_answer` (`answer`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__answers_questions'))
		{
			$query = "DROP TABLE IF EXISTS `#__answers_questions`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__answers_responses'))
		{
			$query = "DROP TABLE IF EXISTS `#__answers_responses`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
