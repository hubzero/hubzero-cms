<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

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