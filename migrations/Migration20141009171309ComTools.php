<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

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
