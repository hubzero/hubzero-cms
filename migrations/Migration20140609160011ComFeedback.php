<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding fields to the feedback table and
 * dropping redundant selected_quotes table
 **/
class Migration20140609160011ComFeedback extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__feedback'))
		{
			if (!$this->db->tableHasField('#__feedback', 'miniquote'))
			{
				$query = "ALTER TABLE `#__feedback` ADD `miniquote` VARCHAR(255)  NOT NULL  DEFAULT '';";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasField('#__feedback', 'admin_rating'))
			{
				$query = "ALTER TABLE `#__feedback` ADD `admin_rating` TINYINT(1)  NOT NULL  DEFAULT '0';";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasField('#__feedback', 'notable_quote'))
			{
				$query = "ALTER TABLE `#__feedback` ADD `notable_quote` TINYINT(1)  NOT NULL  DEFAULT '0';";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasField('#__feedback', 'user_id'))
			{
				$query = "ALTER TABLE `#__feedback` CHANGE `userid` `user_id` INT(11)  NULL  DEFAULT NULL;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__selected_quotes'))
		{
			$query = "SELECT sq.*, f.id AS fid FROM `#__selected_quotes` AS sq LEFT JOIN `#__feedback` AS f ON f.quote=sq.quote AND f.user_id=sq.userid";
			$this->db->setQuery($query);
			if ($results = $this->db->loadObjectList())
			{
				require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_feedback' . DS . 'tables' . DS . 'quotes.php');

				foreach ($results as $result)
				{
					$tbl = new FeedbackQuotes($this->db);
					$tbl->id = $result->fid;
					$tbl->miniquote = $result->miniquote;
					$tbl->notable_quote = $result->notable_quotes;
					$tbl->store();
				}
			}

			$query = "DROP TABLE `#__selected_quotes`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if (!$this->db->tableExists('#__selected_quotes'))
		{
			$query = "CREATE TABLE `#__selected_quotes` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `userid` int(11) DEFAULT '0',
				  `fullname` varchar(100) DEFAULT '',
				  `org` varchar(200) DEFAULT '',
				  `miniquote` varchar(200) DEFAULT '',
				  `short_quote` text,
				  `quote` text,
				  `picture` varchar(250) DEFAULT '',
				  `date` datetime DEFAULT '0000-00-00 00:00:00',
				  `flash_rotation` tinyint(1) DEFAULT '0',
				  `notable_quotes` tinyint(1) DEFAULT '1',
				  `notes` text,
				  PRIMARY KEY (`id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__feedback'))
		{
			if ($this->db->tableHasField('#__feedback', 'miniquote'))
			{
				$query = "ALTER TABLE `#__feedback` DROP COLUMN `miniquote`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__feedback', 'admin_rating'))
			{
				$query = "ALTER TABLE `#__feedback` DROP COLUMN `admin_rating`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__feedback', 'notable_quote'))
			{
				$query = "ALTER TABLE `#__feedback` DROP COLUMN `notable_quote`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__feedback', 'user_id'))
			{
				$query = "ALTER TABLE `#__feedback` CHANGE `user_id` `userid` INT(11)  NULL  DEFAULT NULL;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}