<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for dropping redundant sessionlog index
 **/
class Migration20140804153815Core extends Base
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

		if ($mwdb->tableExists('sessionlog') && $mwdb->tableHasKey('sessionlog', 'sessnum'))
		{
			$query = "ALTER TABLE `sessionlog` DROP INDEX `sessnum`";
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

		/* We can just drop the old tables because they were never used on a live hub */

		if ($mwdb->tableExists('sessionlog') && !$mwdb->tableHasKey('sessionlog', 'sessnum'))
		{
			$query = "CREATE UNIQUE INDEX sessnum ON `sessionlog`(`sessnum`)";
			$mwdb->setQuery($query);
			$mwdb->query();
		}
	}
}