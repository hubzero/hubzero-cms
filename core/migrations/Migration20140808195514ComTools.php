<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding max_uses column to middleware host table
 **/
class Migration20140808195514ComTools extends Base
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

		if ($mwdb->tableExists('host') && !$mwdb->tableHasField('host', 'max_uses'))
		{
			$query = "ALTER TABLE `host` ADD COLUMN `max_uses` int(11) NOT NULL DEFAULT 0";
			$mwdb->setQuery($query);
			$mwdb->query();
		}

		if ($mwdb->tableExists('host') && $mwdb->tableHasField('host', 'uses'))
		{
			$query = "ALTER TABLE `host` CHANGE `uses` `uses` INT(11) NOT NULL DEFAULT 0";
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

		if ($mwdb->tableExists('host') && $mwdb->tableHasField('host', 'max_uses'))
		{
			$query = "ALTER TABLE `host` DROP COLUMN `max_uses`";
			$mwdb->setQuery($query);
			$mwdb->query();
		}

		if ($mwdb->tableExists('host') && $mwdb->tableHasField('host', 'uses'))
		{
			$query = "ALTER TABLE `host` CHANGE `uses` `uses` SMALLINT(5) NOT NULL DEFAULT 0";
			$mwdb->setQuery($query);
			$mwdb->query();
		}
	}
}