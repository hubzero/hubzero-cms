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
 * Migration script for adding max_uses column to middleware host table
 **/
class Migration20140808195514ComTools extends Base
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

		if ($mwdb->tableExists('host') && !$mwdb->tableHasField('host', 'max_uses'))
		{
			$query = "ALTER TABLE `host` ADD COLUMN `max_uses` int(11) NOT NULL DEFAULT 0";
			$mwdb->setQuery($query);
			$mwdb->query();
		}

		if ($mwdb->tableExists('host') && $mwdb->tableHasField('host', 'uses'))
		{
			$query = "ALTER TABLE `host` CHANGE `uses` `uses` INT(11) NOT NULL DEFAULT 0";
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

		if ($mwdb->tableExists('host') && $mwdb->tableHasField('host', 'max_uses'))
		{
			$query = "ALTER TABLE `host` DROP COLUMN `max_uses`";
			$mwdb->setQuery($query);
			$mwdb->query();
		}

		if ($mwdb->tableExists('host') && $mwdb->tableHasField('host', 'uses'))
		{
			$query = "ALTER TABLE `host` CHANGE `uses` `uses` SMALLINT(5) NOT NULL DEFAULT 0";
			$mwdb->setQuery($query);
			$mwdb->query();
		}
	}
}
