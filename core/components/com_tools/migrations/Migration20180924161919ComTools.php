<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration to add primary key to host table
 **/

class Migration20180924161919ComTools extends Base
{
	/**
	 * Up;
	 **/
	public function up()
	{
		if (!$mwdb = $this->getMWDBO())
		{
			$this->setError('Failed to connect to the middleware database', 'warning');
			return false;
		}

		if ($mwdb->tableExists('host'))
		{
			if (!$mwdb->getPrimaryKey('host'))
			{
				$query = "ALTER TABLE `host` ADD PRIMARY KEY (hostname)";
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

		if ($mwdb->tableExists('host'))
		{
			if ($mwdb->getPrimaryKey('host') == 'hostname')
			{
				$query = "ALTER TABLE `host` DROP PRIMARY KEY";
				$mwdb->setQuery($query);
				$mwdb->query();
			}
		}
	}
}
