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
 * Migration script for fixing joblob primary key
 **/
class Migration20140813200031ComTools extends Base
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

		if ($mwdb->tableExists('joblog'))
		{
			$keys    = $mwdb->getTableKeys('joblog');
			$primary = array();
			if ($keys && count($keys) > 0)
			{
				foreach ($keys as $key)
				{
					if ($key->Key_name == "PRIMARY")
					{
						$primary[] = $key->Column_name;
					}
				}

				if (!in_array('venue', $primary))
				{
					$query = "ALTER TABLE `joblog` DROP PRIMARY KEY";
					$mwdb->setQuery($query);
					$mwdb->query();
					$query = "ALTER TABLE `joblog` ADD PRIMARY KEY (`sessnum`, `job`, `event`, `venue`)";
					$mwdb->setQuery($query);
					$mwdb->query();
				}
			}
		}
	}
}
