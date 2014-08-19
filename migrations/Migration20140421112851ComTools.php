<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for removing old venues tables in favor of zones
 **/
class Migration20140421112851ComTools extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$mwdb = $this->getMWDBO())
		{
			$this->setError('Failed to connect to the middleware database', 'warning');
			return false;
		}

		/* We can just drop the old tables because they were never used on a live hub */

		if ($mwdb->tableExists('venues'))
		{
			$query = "DROP TABLE `venues`;";
			$mwdb->setQuery($query);
			$mwdb->query();
		}

		if ($mwdb->tableExists('venue_locations'))
		{
			$query = "DROP TABLE `venue_locations`;";
			$mwdb->setQuery($query);
			$mwdb->query();
		}

		if ($mwdb->tableExists('venue_countries'))
		{
			$query = "DROP TABLE `venue_countries`;";
			$mwdb->setQuery($query);
			$mwdb->query();
		}

		if (!$mwdb->tableExists('zones'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `zones` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `zone` varchar(40) DEFAULT NULL,
			  `title` varchar(255) DEFAULT NULL,
			  `state` varchar(15) DEFAULT NULL,
			  `type` varchar(10) DEFAULT NULL,
			  `master` varchar(255) DEFAULT NULL,
			  `mw_version` varchar(3) DEFAULT NULL,
			  `ssh_key_path` varchar(200) DEFAULT NULL,
			  `picture` varchar(250) DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$mwdb->setQuery($query);
			$mwdb->query();
		}
		if (!$mwdb->tableHasField('zones', 'title'))
		{
			$query = "ALTER TABLE `zones` ADD `title` varchar(255) DEFAULT NULL;";
			$mwdb->setQuery($query);
			$mwdb->query();
		}

		if (!$mwdb->tableExists('zone_locations'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `zone_locations` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `zone_id` int(11) NOT NULL,
			  `ipFROM` int(10) unsigned zerofill NOT NULL DEFAULT '0000000000',
			  `ipTO` int(10) unsigned zerofill NOT NULL DEFAULT '0000000000',
			  `continent` char(2) NOT NULL,
			  `countrySHORT` char(2) NOT NULL,
			  `countryLONG` varchar(64) NOT NULL,
			  `ipREGION` varchar(128) NOT NULL,
			  `ipCITY` varchar(128) NOT NULL,
			  `ipLATITUDE` double DEFAULT NULL,
			  `ipLONGITUDE` double DEFAULT NULL,
			  `notes` varchar(128) DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$mwdb->setQuery($query);
			$mwdb->query();
		}
	}
}
