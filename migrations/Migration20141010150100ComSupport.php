<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding support query folders
 **/
class Migration20141010150100ComSupport extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableHasField('#__support_queries', 'created_by'))
		{
			$query = "ALTER TABLE `#__support_queries` ADD `created_by` INT(11)  NOT NULL  DEFAULT '0';";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableHasField('#__support_queries', 'ordering'))
		{
			$query = "ALTER TABLE `#__support_queries` ADD `ordering` INT(11)  NOT NULL  DEFAULT '0';";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableHasField('#__support_queries', 'folder_id'))
		{
			$query = "ALTER TABLE `#__support_queries` ADD `folder_id` INT(11)  NOT NULL  DEFAULT '0';";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__support_query_folders'))
		{
			$query = "CREATE TABLE `#__support_query_folders` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
				  `title` varchar(200) NOT NULL DEFAULT '',
				  `alias` varchar(200) NOT NULL DEFAULT '',
				  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				  `created_by` int(11) unsigned NOT NULL DEFAULT '0',
				  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				  `modified_by` int(11) unsigned NOT NULL DEFAULT '0',
				  `ordering` int(11) NOT NULL DEFAULT '0',
				  `iscore` tinyint(2) unsigned NOT NULL DEFAULT '0',
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "INSERT INTO `#__support_query_folders` (`id`, `user_id`, `title`, `alias`, `created`, `created_by`, `modified`, `modified_by`, `ordering`, `iscore`)
					VALUES
						(1,0,'Common','common','0000-00-00 00:00:00',0,'0000-00-00 00:00:00',0,1,1),
						(2,0,'Mine','mine','0000-00-00 00:00:00',0,'0000-00-00 00:00:00',0,2,1),
						(3,0,'Custom','custom','0000-00-00 00:00:00',0,'0000-00-00 00:00:00',0,3,1),
						(4,0,'Common','common','0000-00-00 00:00:00',0,'0000-00-00 00:00:00',0,1,2),
						(5,0,'Mine','mine','0000-00-00 00:00:00',0,'0000-00-00 00:00:00',0,2,2);";
			$this->db->setQuery($query);
			$this->db->query();

			/*
				folders:
					1 = common
					2 = mine
					3 = custom
					4 = common (not in acl)
					5 = mine (not in acl)
			*/

			// Update "Common" queries
			$this->db->setQuery("UPDATE `#__support_queries` SET `folder_id`=1 WHERE `iscore`=2 AND `folder_id`=0");
			$this->db->query();

			// Update "Mine" queries
			$this->db->setQuery("UPDATE `#__support_queries` SET `folder_id`=2 WHERE `iscore`=1 AND `folder_id`=0");
			$this->db->query();

			// Update "Common not in ACL" queries
			$this->db->setQuery("UPDATE `#__support_queries` SET `folder_id`=4 WHERE `iscore`=4 AND `folder_id`=0");
			$this->db->query();

			// Get all the "mine" queries
			$this->db->setQuery("SELECT * FROM `#__support_queries` WHERE `folder_id`=2 ORDER BY `id`");
			if ($queries = $this->db->loadObjectList())
			{
				include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_support' . DS . 'tables' . DS . 'query.php');

				// Copy the queries to the new folder
				foreach ($queries as $k => $query)
				{
					$stq = new SupportQuery($this->db);
					$stq->bind($query);
					$stq->id        = null;
					$stq->user_id   = 0;
					$stq->folder_id = 5;
					$stq->ordering  = $k;
					$stq->iscore    = 4;
					$stq->store();
				}
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableHasField('#__support_queries', 'created_by'))
		{
			$query = "ALTER TABLE `#__support_queries` DROP `created_by`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableHasField('#__support_queries', 'ordering'))
		{
			$query = "ALTER TABLE `#__support_queries` DROP `ordering`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableHasField('#__support_queries', 'folder_id'))
		{
			$query = "ALTER TABLE `#__support_queries` DROP `folder_id`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__support_query_folders'))
		{
			$query = "DROP TABLE `#__support_query_folders`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}