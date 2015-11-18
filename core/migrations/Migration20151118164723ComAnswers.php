<?php

use Hubzero\Content\Migration\Base;

// Restricted access
defined('_HZEXEC_') or die();

/**
 * Migration script for moving voting logs to #__item_votes
 **/
class Migration20151118164723ComAnswers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__answers_questions_log'))
		{
			$this->db->setQuery("SELECT * FROM `#__answers_questions_log`");
			$rows = $this->db->loadObjectList();
			foreach ($rows as $row)
			{
				$query = "INSERT INTO `#__item_votes` (`id`, `item_id`, `item_type`, `ip`, `created`, `created_by`, `vote`)
						VALUES (null, " . $this->db->quote($row->question_id)  . ", " . $this->db->quote('question')  . ", " . $this->db->quote($row->ip)  . ", " . $this->db->quote($row->expires)  . ", " . $this->db->quote($row->voter)  . ", 0)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			$this->db->setQuery("DROP TABLE IF EXISTS `#__answers_questions_log`");
			$this->db->query();
		}

		if ($this->db->tableExists('#__answers_log'))
		{
			$this->db->setQuery("SELECT * FROM `#__answers_log`");
			$rows = $this->db->loadObjectList();
			foreach ($rows as $row)
			{
				$query = "INSERT INTO `#__item_votes` (`id`, `item_id`, `item_type`, `ip`, `created`, `created_by`, `vote`)
						VALUES (null, " . $this->db->quote($row->response_id)  . ", " . $this->db->quote('response')  . ", " . $this->db->quote($row->ip)  . ", " . $this->db->quote('0000-00-00 00:00:00')  . ", 0, " . $this->db->quote($row->helpful == 'yes' ? 1 : -1) . ")";
				$this->db->setQuery($query);
				$this->db->query();
			}

			$this->db->setQuery("DROP TABLE IF EXISTS `#__answers_log`");
			$this->db->query();
		}

		if ($this->db->tableExists('#__answers_questions'))
		{
			if (!$this->db->tableHasField('#__answers_questions', 'nothelpful'))
			{
				$query = "ALTER TABLE `#__answers_questions` ADD COLUMN `nothelpful` INT(11) NOT NULL DEFAULT '0';";
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
		if (!$this->db->tableExists('#__answers_questions_log'))
		{
			$this->db->setQuery("SELECT * FROM `#__item_votes` WHERE `item_type`=" . $this->db->quote('question'));
			$rows = $this->db->loadObjectList();
			foreach ($rows as $row)
			{
				$query = "INSERT INTO `#__answers_questions_log` (`id`, `question_id`, `expires`, `voter`, `ip`)
						VALUES (null, " . $this->db->quote($row->item_id)  . ", " . $this->db->quote($row->created) . ", " . $this->db->quote($row->created_by) . ", " . $this->db->quote($row->ip) . ")";
				$this->db->setQuery($query);
				$this->db->query();
			}

			$this->db->setQuery(
				"CREATE TABLE `#__answers_questions_log` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `question_id` int(11) unsigned NOT NULL DEFAULT '0',
				  `expires` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				  `voter` int(11) NOT NULL DEFAULT '0',
				  `ip` varchar(15) NOT NULL DEFAULT '',
				  PRIMARY KEY (`id`),
				  KEY `idx_qid` (`question_id`),
				  KEY `idx_voter` (`voter`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;"
			);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__answers_log'))
		{
			$this->db->setQuery("SELECT * FROM `#__item_votes` WHERE `item_type`=" . $this->db->quote('response'));
			$rows = $this->db->loadObjectList();
			foreach ($rows as $row)
			{
				$query = "INSERT INTO `#__answers_log` (`id`, `response_id`, `ip`, `helpful`)
						VALUES (null, " . $this->db->quote($row->item_id)  . ", " . $this->db->quote($row->ip)  . ", " . $this->db->quote($row->vote == 1 ? 'yes' : 'no') . ")";
				$this->db->setQuery($query);
				$this->db->query();
			}

			$this->db->setQuery(
				"CREATE TABLE `#__answers_log` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `response_id` int(11) unsigned NOT NULL DEFAULT '0',
				  `ip` varchar(15) NOT NULL DEFAULT '',
				  `helpful` varchar(10) NOT NULL DEFAULT '',
				  PRIMARY KEY (`id`),
				  KEY `idx_rid` (`response_id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;"
			);
			$this->db->query();
		}

		if ($this->db->tableExists('#__answers_questions'))
		{
			if ($this->db->tableHasField('#__answers_questions', 'nothelpful'))
			{
				$query = "ALTER TABLE `#__answers_questions` DROP COLUMN `nothelpful`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}