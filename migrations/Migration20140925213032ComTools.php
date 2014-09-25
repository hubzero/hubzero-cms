<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding new token field to job table
 **/
class Migration20140925213032ComTools extends Base
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

		if ($mwdb->tableExists('job')
			&& $mwdb->tableHasField('job', 'active')
			&& !$mwdb->tableHasField('job', 'jobtoken'))
		{
			$query = "ALTER TABLE `job` ADD `jobtoken` VARCHAR(32) NULL DEFAULT NULL AFTER `active`";
			$mwdb->setQuery($query);
			$mwdb->query();
		}

		if ($mwdb->tableExists('job')
			&& $mwdb->tableHasField('job', 'jobtoken')
			&& $mwdb->tableHasField('job', 'username')
			&& !$mwdb->tableHasKey('job', 'idx_username_jobtoken'))
		{
			$query = "ALTER TABLE `job` ADD KEY `idx_username_jobtoken` (`username`,`jobtoken`)";
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

		if ($mwdb->tableExists('job') && $mwdb->tableHasKey('job', 'idx_username_jobtoken'))
		{
			$query = "ALTER TABLE `job` DROP KEY `idx_username_jobtoken`";
			$mwdb->setQuery($query);
			$mwdb->query();
		}

		if ($mwdb->tableExists('job') && $mwdb->tableHasField('job', 'jobtoken'))
		{
			$query = "ALTER TABLE `job` DROP `jobtoken`";
			$mwdb->setQuery($query);
			$mwdb->query();
		}
	}
}