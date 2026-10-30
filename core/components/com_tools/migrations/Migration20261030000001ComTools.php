<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding a hard timeout, in seconds, to middleware sessions
 **/
class Migration20261030000001ComTools extends Base
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

		if ($mwdb->tableExists('session') && !$mwdb->tableHasField('session', 'hardTimeout'))
		{
			$query = "ALTER TABLE `session` ADD COLUMN `hardTimeout` int(11) DEFAULT NULL AFTER `timeout`";
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

		if ($mwdb->tableExists('session') && $mwdb->tableHasField('session', 'hardTimeout'))
		{
			$query = "ALTER TABLE `session` DROP COLUMN `hardTimeout`";
			$mwdb->setQuery($query);
			$mwdb->query();
		}
	}
}
