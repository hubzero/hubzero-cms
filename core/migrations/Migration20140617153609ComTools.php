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
