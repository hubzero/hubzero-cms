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
 * Migration to add primary key to display table
 **/

class Migration20180924160808ComTools extends Base
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

		if ($mwdb->tableExists('display'))
		{
			$keys = $mwdb->getTableKeys('display');
			$primary_keys = array();
			if (is_array($keys) && (count($keys) > 0))
			{
				foreach ($keys as $k)
				{
					if ($k->Key_name == 'PRIMARY')
					{
						$primary_keys[] = $k->Column_name;
					}

				}
			}
			if (count($primary_keys) == 0)
			{
				$query = "ALTER TABLE `display` ADD PRIMARY KEY (hostname, dispnum)";
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

		if ($mwdb->tableExists('display'))
		{
			if ($mwdb->getPrimaryKey('display') == 'hostname' || $mwdb->getPrimaryKey('display') == 'dispnum')
			{
				$query = "ALTER TABLE `display` DROP PRIMARY KEY";
				$mwdb->setQuery($query);
				$mwdb->query();
			}
		}
	}
}
