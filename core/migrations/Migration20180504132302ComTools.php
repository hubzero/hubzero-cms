<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for expire-session daemon, to record end time for jobs automatically with a timestamp
 **/
class Migration20180504132302ComTools extends Base
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

		// Resize column hostname
		if ($mwdb->tableExists('host') && !$mwdb->tableHasField('host', 'hostname')) {
			$query = "ALTER TABLE `host` CHANGE `hostname` `hostname` varchar(80) NOT NULL DEFAULT '';";
			$mwdb->setQuery($query);
			$mwdb->query();
		}
		// Resize column hostname
		if ($mwdb->tableExists('display') && !$mwdb->tableHasField('display', 'hostname')) {
			$query = "ALTER TABLE `display` CHANGE `hostname` `hostname` varchar(80) NOT NULL DEFAULT '';";
			$mwdb->setQuery($query);
			$mwdb->query();
		}
		// Resize column exechost
		if ($mwdb->tableExists('session') && !$mwdb->tableHasField('session', 'exechost')) {
			$query = "ALTER TABLE `session` CHANGE `exechost` `exechost` varchar(80) NOT NULL DEFAULT '';";
			$mwdb->setQuery($query);
			$mwdb->query();
		}
		// Resize column exechost
		if ($mwdb->tableExists('sessionlog') && !$mwdb->tableHasField('sessionlog', 'exechost')) {
			$query = "ALTER TABLE `sessionlog` CHANGE `exechost` `exechost` varchar(80) NOT NULL DEFAULT '';";
			$mwdb->setQuery($query);
			$mwdb->query();
		}
		// Resize column remotehost
		if ($mwdb->tableExists('sessionlog') && !$mwdb->tableHasField('sessionlog', 'remotehost')) {
			$query = "ALTER TABLE `sessionlog` CHANGE `remotehost` `remotehost` varchar(80) NOT NULL DEFAULT '';";
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

		// Resize column hostname
		if ($mwdb->tableExists('host') && !$mwdb->tableHasField('host', 'hostname')) {
			$query = "ALTER TABLE `host` CHANGE `hostname` `hostname` varchar(40) NOT NULL DEFAULT '';";
			$mwdb->setQuery($query);
			$mwdb->query();
		}
		// Resize column hostname
		if ($mwdb->tableExists('display') && !$mwdb->tableHasField('display', 'hostname')) {
			$query = "ALTER TABLE `display` CHANGE `hostname` `hostname` varchar(40) NOT NULL DEFAULT '';";
			$mwdb->setQuery($query);
			$mwdb->query();
		}
		// Resize column exechost
		if ($mwdb->tableExists('session') && !$mwdb->tableHasField('session', 'exechost')) {
			$query = "ALTER TABLE `session` CHANGE `exechost` `exechost` varchar(40) NOT NULL DEFAULT '';";
			$mwdb->setQuery($query);
			$mwdb->query();
		}
		// Resize column exechost
		if ($mwdb->tableExists('session') && !$mwdb->tableHasField('session', 'exechost')) {
			$query = "ALTER TABLE `session` CHANGE `exechost` `exechost` varchar(40) NOT NULL DEFAULT '';";
			$mwdb->setQuery($query);
			$mwdb->query();
		}
		// Resize column exechost
		if ($mwdb->tableExists('sessionlog') && !$mwdb->tableHasField('sessionlog', 'exechost')) {
			$query = "ALTER TABLE `sessionlog` CHANGE `exechost` `exechost` varchar(40) NOT NULL DEFAULT '';";
			$mwdb->setQuery($query);
			$mwdb->query();
		}
		// Resize column remotehost
		if ($mwdb->tableExists('sessionlog') && !$mwdb->tableHasField('sessionlog', 'remotehost')) {
			$query = "ALTER TABLE `sessionlog` CHANGE `remotehost` `remotehost` varchar(40) NOT NULL DEFAULT '';";
			$mwdb->setQuery($query);
			$mwdb->query();
		}
	}
}
