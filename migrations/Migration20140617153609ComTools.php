<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding zone_id to sessionlog and joblog tables
 **/
class Migration20140617153609ComTools extends Base
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

		if (!$mwdb->tableHasField('sessionlog', 'zone_id'))
		{
			$query = "ALTER TABLE `sessionlog` ADD `zone_id` int(11) NOT NULL DEFAULT '0'";
			$mwdb->setQuery($query);
			$mwdb->query();
		}
		if (!$mwdb->tableHasField('joblog', 'zone_id'))
		{
			$query = "ALTER TABLE `joblog` ADD `zone_id` int(11) NOT NULL DEFAULT '0'";
			$mwdb->setQuery($query);
			$mwdb->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if (!$mwdb = $this->getMWDBO())
		{
			$this->setError('Failed to connect to the middleware database', 'warning');
			return false;
		}

		if ($mwdb->tableHasField('sessionlog', 'zone_id'))
		{
			$query = "ALTER TABLE `sessionlog` DROP `zone_id`";
			$mwdb->setQuery($query);
			$mwdb->query();
		}
		if ($mwdb->tableHasField('joblog', 'zone_id'))
		{
			$query = "ALTER TABLE `joblog` DROP `zone_id`";
			$mwdb->setQuery($query);
			$mwdb->query();
		}
	}
}