<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding support statuses
 **/
class Migration20140814103014ComSupport extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__support_statuses'))
		{
			// Create the table
			$query = "CREATE TABLE `#__support_statuses` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `open` tinyint(2) NOT NULL DEFAULT '0',
				  `title` varchar(250) NOT NULL DEFAULT '',
				  `alias` char(250) NOT NULL DEFAULT '',
				  PRIMARY KEY (`id`),
				  KEY `idx_open` (`open`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();

			// Add default values
			$query = "INSERT INTO `#__support_statuses` (`id`, `open`, `title`, `alias`)
					VALUES
						(1,1,'Open','open'),
						(2,1,'Waiting response','waiting'),
						(3,1,'Waiting review','waitingreview'),
						(4,1,'Pending update','pendingupdate')";

			$this->db->setQuery("SELECT * FROM `#__support_resolutions`");
			if ($results = $this->db->loadObjectList())
			{
				$i = 5;
				$entries = array();
				foreach ($results as $result)
				{
					$entries[] = "(" . $i . ",0," . $this->db->quote($result->title) . "," . $this->db->quote($result->alias) . ")";
					$i++;
				}

				if (count($entries))
				{
					$query .= "," . implode(",", $entries);
				}
			}

			$this->db->setQuery($query);
			$this->db->query();

			// Update closed tickets
			$this->db->setQuery("SELECT * FROM `#__support_statuses` WHERE `open`=0");
			if ($rows = $this->db->loadObjectList())
			{
				foreach ($rows as $row)
				{
					$this->db->setQuery("UPDATE `#__support_tickets` SET `status`=" . $this->db->quote($row->id) . " WHERE `resolved`=" . $this->db->quote($row->alias));
					$this->db->query();
				}
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__support_statuses'))
		{
			$this->db->setQuery("DROP TABLE IF EXISTS `#__support_statuses`");
			$this->db->query();
		}
	}
}