<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding OAI-PMH plugin entries for resources and publications
 **/
class Migration20150218180139PlgOaipmh extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__oaipmh_dcspecs'))
		{
			$query = "DROP TABLE `#__oaipmh_dcspecs`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		$this->addPluginEntry('oaipmh', 'resources', 1);
		$this->addPluginEntry('oaipmh', 'publications', 1);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if (!$this->db->tableExists('#__oaipmh_dcspecs'))
		{
			$query = "CREATE TABLE `#__oaipmh_dcspecs` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `name` varchar(255) NOT NULL,
			  `query` text NOT NULL,
			  `display` int(1) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1";
			$this->db->setQuery($query);
			$this->db->query();
		}

		$this->deletePluginEntry('oaipmh', 'resources');
		$this->deletePluginEntry('oaipmh', 'publications');
	}
}