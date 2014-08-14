<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

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
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_tools' . DS . 'models' . DS . 'mw.utils.php');

		$mwdb = MwUtils::getMWDBO();

		if (!$mwdb->connected())
		{
			$return = new \stdClass();
			$return->error = new \stdClass();
			$return->error->type = 'warning';
			$return->error->message = 'Failed to connect to the middleware database';
			return $return;
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