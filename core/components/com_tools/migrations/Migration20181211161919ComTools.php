<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration to add index for job column on joblog table
 **/

class Migration20181211161919ComTools extends Base
{
	/**
	 * Up;
	 **/
	public function up()
	{
		if (!$mwdb = $this->getMWDBO())
		{
			$this->setError('Failed to connect to the middleware database', 'warning');
			return false;
		}

		if ($mwdb->tableExists('joblog'))
		{
			if (!$mwdb->tableHasKey('joblog', 'idx_job'))
			{
				$query = "ALTER TABLE `joblog` ADD INDEX `idx_job` (`job`)";
				$mwdb->setQuery($query);
				$mwdb->query();
			}
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

		if ($mwdb->tableExists('joblog'))
		{
			if ($mwdb->tableHasKey('joblog', 'idx_job'))
			{
				$query = "ALTER TABLE `joblog` DROP KEY `idx_job`";
				$mwdb->setQuery($query);
				$mwdb->query();
			}
		}
	}
}
