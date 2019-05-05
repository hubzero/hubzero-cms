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
 * Migration script for renaming venue_id to zone_id on host table
 **/
class Migration20140505141538ComTools extends Base
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

		if ($mwdb->tableExists('host')
			&& $mwdb->tableHasField('host', 'venue_id')
			&& !$mwdb->tableHasField('host', 'zone_id'))
		{
			$query = "ALTER TABLE `host` CHANGE `venue_id` `zone_id` INT(11) NULL DEFAULT NULL";
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

		if ($mwdb->tableExists('host')
			&& !$mwdb->tableHasField('host', 'venue_id')
			&& $mwdb->tableHasField('host', 'zone_id'))
		{
			$query = "ALTER TABLE `host` CHANGE `zone_id` `venue_id` INT(11) NULL DEFAULT NULL";
			$mwdb->setQuery($query);
			$mwdb->query();
		}
	}
}
