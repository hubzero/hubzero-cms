<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for 2011/12 middleware table modifications
 **/
class Migration20120101000002Core extends Base
{
	public function up()
	{
		if (!$mwdb = $this->getMWDBO())
		{
			$this->setError('Failed to connect to the middleware database', 'warning');
			return false;
		}

		if ($mwdb->tableExists('display') && $mwdb->tableHasField('display', 'hostname') && !$mwdb->tableHasKey('display', 'idx_hostname'))
		{
			$query = "ALTER TABLE `display` ADD INDEX `idx_hostname` (`hostname` ASC)";
			$mwdb->setQuery($query);
			$mwdb->query();
		}

		if ($mwdb->tableExists('host') && $mwdb->tableHasField('host', 'hostname') && !$mwdb->tableHasKey('host', 'PRIMARY'))
		{
			$query = "ALTER TABLE `host` ADD PRIMARY KEY (`hostname`)";
			$mwdb->setQuery($query);
			$mwdb->query();
		}

		if ($mwdb->tableExists('hosttype') && $mwdb->tableHasKey('hosttype', 'PRIMARY}'))
		{
			$query = "ALTER TABLE `hosttype` DROP PRIMARY KEY";
			$mwdb->setQuery($query);
			$mwdb->query();
		}

		if ($mwdb->tableExists('job'))
		{
			if ($mwdb->tableHasField('job', 'start') && !$mwdb->tableHasKey('job', 'idx_start'))
			{
				$query = "ALTER TABLE `job` ADD INDEX `idx_start` (`start` ASC)";
				$mwdb->setQuery($query);
				$mwdb->query();
			}

			if ($mwdb->tableHasField('job', 'heartbeat') && !$mwdb->tableHasKey('job', 'idx_heartbeat'))
			{
				$query = "ALTER TABLE `job` ADD INDEX `idx_heartbeat` (`heartbeat` ASC)";
				$mwdb->setQuery($query);
				$mwdb->query();
			}
		}

		if ($mwdb->tableExists('joblog'))
		{
			if ($mwdb->tableHasField('joblog', 'walltime'))
			{
				$query = "ALTER TABLE `joblog` CHANGE COLUMN `walltime` `walltime` DOUBLE UNSIGNED NULL DEFAULT '0'";
				$mwdb->setQuery($query);
				$mwdb->query();
			}

			if ($mwdb->tableHasField('joblog', 'cputime'))
			{
				$query = "ALTER TABLE `joblog` CHANGE COLUMN `cputime` `cputime` DOUBLE UNSIGNED NULL DEFAULT '0'";
				$mwdb->setQuery($query);
				$mwdb->query();
			}

			if ($mwdb->tableHasKey('joblog', 'PRIMARY'))
			{
				$query = "ALTER TABLE `joblog` DROP PRIMARY KEY ";
				$mwdb->setQuery($query);
				$mwdb->query();
			}

			if (!$mwdb->tableHasKey('joblog', 'PRIMARY'))
			{
				$query = "ALTER TABLE `joblog` ADD PRIMARY KEY (`sessnum`, `job`, `event`, `venue`)";
				$mwdb->setQuery($query);
				$mwdb->query();
			}
		}

		if ($mwdb->tableExists('session') && $mwdb->tableHasField('session', 'sessname'))
		{
			$query = "ALTER TABLE `session` CHANGE COLUMN `sessname` `sessname` VARCHAR(100) NOT NULL DEFAULT ''";
			$mwdb->setQuery($query);
			$mwdb->query();
		}

		if ($mwdb->tableExists('sessionlog'))
		{
			if ($mwdb->tableHasField('sessionlog', 'sessnum'))
			{
				$query = "ALTER TABLE `sessionlog` CHANGE COLUMN `sessnum` `sessnum` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT";
				$mwdb->setQuery($query);
				$mwdb->query();
			}

			if ($mwdb->tableHasField('sessionlog', 'walltime'))
			{
				$query = "ALTER TABLE `sessionlog` CHANGE COLUMN `walltime` `walltime` DOUBLE UNSIGNED NULL DEFAULT '0'";
				$mwdb->setQuery($query);
				$mwdb->query();
			}

			if ($mwdb->tableHasField('sessionlog', 'viewtime'))
			{
				$query = "ALTER TABLE `sessionlog` CHANGE COLUMN `viewtime` `viewtime` DOUBLE UNSIGNED NULL DEFAULT '0'";
				$mwdb->setQuery($query);
				$mwdb->query();
			}

			if ($mwdb->tableHasField('sessionlog', 'cputime'))
			{
				$query = "ALTER TABLE `sessionlog` CHANGE COLUMN `cputime` `cputime` DOUBLE UNSIGNED NULL DEFAULT '0'";
				$mwdb->setQuery($query);
				$mwdb->query();
			}
		}

		if ($mwdb->tableExists('view') && $mwdb->tableHasField('view', 'referrer'))
		{
			$query = "ALTER TABLE `view` DROP COLUMN `referrer`";
			$mwdb->setQuery($query);
			$mwdb->query();
		}
	}
}
