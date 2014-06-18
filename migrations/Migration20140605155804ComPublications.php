<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding logs table
 **/
class Migration20140605155804ComPublications extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		// Set up curation
		if (!$this->db->tableExists('#__publication_logs'))
		{
			$query = "CREATE TABLE `#__publication_logs` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `publication_id` int(11) NOT NULL,
			  `publication_version_id` int(11) NOT NULL,
			  `month` int(2) NOT NULL,
			  `year` int(2) NOT NULL,
			  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			  `page_views` int(11) DEFAULT '0',
			  `primary_accesses` int(11) DEFAULT '0',
			  `support_accesses` int(11) DEFAULT '0',
			  PRIMARY KEY (`id`)
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
		$queries = array();

		if ($this->db->tableExists('#__publication_logs'))
		{
			$query = "DROP TABLE IF EXISTS `#__publication_curation_history`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}