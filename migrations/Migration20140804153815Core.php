<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for dropping redundant sessionlog index
 **/
class Migration20140804153815Core extends Base
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

		/* We can just drop the old tables because they were never used on a live hub */

		if ($mwdb->tableExists('sessionlog') && $mwdb->tableHasKey('sessionlog', 'sessnum'))
		{
			$query = "ALTER TABLE `sessionlog` DROP INDEX `sessnum`";
			$mwdb->setQuery($query);
			$mwdb->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
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

		/* We can just drop the old tables because they were never used on a live hub */

		if ($mwdb->tableExists('sessionlog') && !$mwdb->tableHasKey('sessionlog', 'sessnum'))
		{
			$query = "CREATE UNIQUE INDEX sessnum ON `sessionlog`(`sessnum`)";
			$mwdb->setQuery($query);
			$mwdb->query();
		}
	}
}