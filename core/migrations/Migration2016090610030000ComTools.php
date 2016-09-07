<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script to fix index naming conventions in middleware tables
 **/
class Migration2016090610030000ComTools extends Base
{
	public function up()
	{
		if (!$mwdb = $this->getMWDBO())
		{
			$this->setError('Failed to connect to the middleware database', 'warning');
			return false;
		}

		if ($mwdb->tableExists('view') && $mwdb->tableHasField('view', 'viewid') && !$mwdb->tableHasKey('view', 'PRIMARY'))
		{
			$query = "ALTER TABLE `view` ADD PRIMARY KEY (`viewid`)";
			$mwdb->setQuery($query);
			$mwdb->query();
		}

		if ($mwdb->tableExists('view') && $mwdb->tableHasField('view', 'viewid') && $mwdb->tableHasKey('view', 'viewid'))
		{
			$query = "DROP INDEX `viewid` ON `view`;";
			$mwdb->setQuery($query);
			$mwdb->query();
		}

		if ($mwdb->tableExists('sessionpriv') && $mwdb->tableHasField('sessionpriv', 'privid') && !$mwdb->tableHasKey('sessionpriv', 'PRIMARY'))
		{
			$query = "ALTER TABLE `sessionpriv` ADD PRIMARY KEY (`privid`)";
			$mwdb->setQuery($query);
			$mwdb->query();
		}

		if ($mwdb->tableExists('sessionpriv') && $mwdb->tableHasField('sessionpriv', 'privid') && $mwdb->tableHasKey('sessionpriv', 'privid'))
		{
			$query = "DROP INDEX `privid` ON `sessionpriv`;";
			$mwdb->setQuery($query);
			$mwdb->query();
		}
	
		if ($mwdb->tableExists('session') && $mwdb->tableHasField('session', 'sessnum') && !$mwdb->tableHasKey('session', 'PRIMARY'))
		{
			$query = "ALTER TABLE `session` ADD PRIMARY KEY (`sessnum`)";
			$mwdb->setQuery($query);
			$mwdb->query();
		}

		if ($mwdb->tableExists('session') && $mwdb->tableHasField('session', 'sessnum') && $mwdb->tableHasKey('session', 'sessnum'))
		{
			$query = "DROP INDEX `sessnum` ON `session`;";
			$mwdb->setQuery($query);
			$mwdb->query();
		}

		if ($mwdb->tableExists('joblog') && $mwdb->tableHasField('joblog', 'sessnum') && !$mwdb->tableHasKey('joblog', 'idx_sessnum'))
		{
			$query = "ALTER TABLE `joblog` ADD KEY idx_sessnum (`sessnum`)";
			$mwdb->setQuery($query);
			$mwdb->query();
		}

		if ($mwdb->tableExists('joblog') && $mwdb->tableHasField('joblog', 'sessnum') && $mwdb->tableHasKey('joblog', 'sessnum'))
		{
			$query = "DROP INDEX `sessnum` ON `joblog`;";
			$mwdb->setQuery($query);
			$mwdb->query();
		}

		if ($mwdb->tableExists('joblog') && $mwdb->tableHasField('joblog', 'event') && !$mwdb->tableHasKey('joblog', 'idx_event'))
		{
			$query = "ALTER TABLE `joblog` ADD KEY idx_event (`event`)";
			$mwdb->setQuery($query);
			$mwdb->query();
		}

		if ($mwdb->tableExists('joblog') && $mwdb->tableHasField('joblog', 'event') && $mwdb->tableHasKey('joblog', 'event'))
		{
			$query = "DROP INDEX `event` ON `joblog`;";
			$mwdb->setQuery($query);
			$mwdb->query();
		}

		if ($mwdb->tableExists('job') && $mwdb->tableHasField('job', 'jobid') && !$mwdb->tableHasKey('job', 'uidx_jobid'))
		{
			$query = "ALTER TABLE `job` ADD UNIQUE KEY uidx_jobid (`jobid`)";
			$mwdb->setQuery($query);
			$mwdb->query();
		}

		if ($mwdb->tableExists('job') && $mwdb->tableHasKey('job', 'jobid'))
		{
			$query = "DROP INDEX `jobid` ON `job`;";
			$mwdb->setQuery($query);
			$mwdb->query();
		}

		if ($mwdb->tableExists('job') && $mwdb->tableHasKey('job', 'start'))
		{
			$query = "DROP INDEX `start` ON `job`;";
			$mwdb->setQuery($query);
			$mwdb->query();
		}

		if ($mwdb->tableExists('job') && $mwdb->tableHasKey('job', 'start_2'))
		{
			$query = "DROP INDEX `start_2` ON `job`;";
			$mwdb->setQuery($query);
			$mwdb->query();
		}

		if ($mwdb->tableExists('job') && $mwdb->tableHasKey('job', 'heartbeat_2'))
		{
			$query = "DROP INDEX `heartbeat_2` ON `job`;";
			$mwdb->setQuery($query);
			$mwdb->query();
		}

		if ($mwdb->tableExists('job') && $mwdb->tableHasKey('job', 'heartbeat'))
		{
			$query = "DROP INDEX `heartbeat` ON `job`;";
			$mwdb->setQuery($query);
			$mwdb->query();
		}

		if ($mwdb->tableExists('domainclass') && $mwdb->tableHasField('domainclass', 'class') && !$mwdb->tableHasKey('domainclass', 'idx_class'))
		{
			$query = "ALTER TABLE `domainclass` ADD KEY idx_class (`class`)";
			$mwdb->setQuery($query);
			$mwdb->query();
		}

		if ($mwdb->tableExists('domainclass') && $mwdb->tableHasKey('domainclass', 'class'))
		{
			$query = "DROP INDEX `class` ON `domainclass`;";
			$mwdb->setQuery($query);
			$mwdb->query();
		}

		if ($mwdb->tableExists('domainclass') && $mwdb->tableHasField('domainclass', 'class') && !$mwdb->tableHasKey('domainclass', 'idx_domain_class'))
		{
			$query = "ALTER TABLE `domainclass` ADD KEY idx_domain_class (`domain`,`class`) USING BTREE";
			$mwdb->setQuery($query);
			$mwdb->query();
		}

		if ($mwdb->tableExists('domainclass') && $mwdb->tableHasKey('domainclass', 'domain'))
		{
			$query = "DROP INDEX `domain` ON `domainclass`;";
			$mwdb->setQuery($query);
			$mwdb->query();
		}

		if ($mwdb->tableExists('display') && $mwdb->tableHasKey('display', 'hostname'))
		{
			$query = "DROP INDEX `hostname` ON `display`;";
			$mwdb->setQuery($query);
			$mwdb->query();
		}
	}

	public function down()
	{
	}
}
