<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding description field to zones
 **/
class Migration20150330174214ComTools extends Base
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

		if ($mwdb->tableExists('zones'))
		{
			if (!$mwdb->tableHasField('zones', 'description'))
			{
				$query = "ALTER TABLE `zones` ADD `description` TEXT;";
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

		if ($mwdb->tableExists('zones'))
		{
			if ($mwdb->tableHasField('zones', 'description'))
			{
				$query = "ALTER TABLE `zones` DROP `description`;";
				$mwdb->setQuery($query);
				$mwdb->query();
			}
		}
	}
}