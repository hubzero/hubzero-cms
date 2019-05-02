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
 * Migration to add primary key to hosttype table
 **/

class Migration20180924153232ComTools extends Base
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

		if ($mwdb->tableExists('hosttype'))
		{
			if (!$mwdb->tableHasKey('hosttype', 'name') && !$mwdb->getPrimaryKey('hosttype'))
			{
				$query = "ALTER TABLE `hosttype` ADD PRIMARY KEY (name)";
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

		if ($mwdb->tableExists('hosttype'))
		{
			if ($mwdb->getPrimaryKey('hosttype') == 'name')
			{
				$query = "ALTER TABLE `hosttype` DROP PRIMARY KEY";
				$mwdb->setQuery($query);
				$mwdb->query();
			}
		}
	}
}
