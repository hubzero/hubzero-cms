<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for setting up publication building blocks
 **/
class Migration20140519120000ComPublications extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$queries = array();

		// Set up curation
		if (!$this->db->tableExists('#__publication_curation_history'))
		{
			$queries[] = "CREATE TABLE IF NOT EXISTS `#__publication_curation_history` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`publication_version_id` int(11) NOT NULL DEFAULT '0',
				`created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`created_by` int(11) NOT NULL DEFAULT '0',
				`changelog` text NOT NULL DEFAULT '',
				`curator` tinyint(3) NOT NULL DEFAULT '0',
				`oldstatus` int(11) NOT NULL DEFAULT '0',
				`newstatus` int(11) NOT NULL DEFAULT '0',
				PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		}

		// Run queries
		if (count($queries) > 0)
		{
			// Run queries
			foreach ($queries as $query)
			{
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
		$queries = array();

		if ($this->db->tableExists('#__publication_curation_history'))
		{
			$queries[] = "DROP TABLE IF EXISTS `#__publication_curation_history`";
		}

		if (count($queries) > 0)
		{
			// Run queries
			foreach ($queries as $query)
			{
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}