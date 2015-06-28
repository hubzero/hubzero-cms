<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding default zone field
 **/
class Migration20150414183059ComTools extends Base
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

		if ($mwdb->tableExists('zones')
		 && $mwdb->tableHasField('zones', 'state')
		&& !$mwdb->tableHasField('zones', 'is_default'))
		{
			$query = "ALTER TABLE `zones` ADD `is_default` TINYINT(2) NOT NULL DEFAULT '0' AFTER `state`";
			$mwdb->setQuery($query);
			$mwdb->query();

			// Set the first zone as default
			$query = "UPDATE `zones` SET `is_default` = 1 WHERE `type` = 'local' ORDER BY `id` ASC LIMIT 1";
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

		if ($mwdb->tableExists('zones')
		 && $mwdb->tableHasField('zones', 'is_default'))
		{
			$query = "ALTER TABLE `zones` DROP `is_default`";
			$mwdb->setQuery($query);
			$mwdb->query();
		}
	}
}