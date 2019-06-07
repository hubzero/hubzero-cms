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
 * Migration script for removing fields in a few middleware tables
 **/
class Migration20141009171309ComTools extends Base
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

		if ($mwdb->tableExists('host') && $mwdb->tableHasField('host', 'venue_id'))
		{
			$query = "ALTER TABLE `host` DROP COLUMN `venue_id`";
			$mwdb->setQuery($query);
			$mwdb->query();
		}

		if ($mwdb->tableExists('zones') && $mwdb->tableHasField('zones', 'picture'))
		{
			$query = "ALTER TABLE `zones` DROP COLUMN `picture`";
			$mwdb->setQuery($query);
			$mwdb->query();
		}

		if ($mwdb->tableExists('zones') && $mwdb->tableHasField('zones', 'title') && $mwdb->tableHasField('zones', 'zone'))
		{
			$query = "ALTER TABLE `zones` CHANGE COLUMN `title` `title` VARCHAR(255) NULL DEFAULT NULL AFTER `zone`";
			$mwdb->setQuery($query);
			$mwdb->query();
		}
	}
}
