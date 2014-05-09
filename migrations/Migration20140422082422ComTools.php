<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script to add params to session table
 **/
class Migration20140422082422ComTools extends Base
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

		if (!$mwdb->tableHasField('session', 'params'))
		{
			$query = "ALTER TABLE `session` ADD `params` TEXT  NULL;";
			$mwdb->setQuery($query);
			$mwdb->query();
		}
		if (!$mwdb->tableHasField('session', 'zone_id'))
		{
			$query = "ALTER TABLE `session` ADD `zone_id` int(11) NOT NULL DEFAULT '0';";
			$mwdb->setQuery($query);
			$mwdb->query();
		}
	}

	/**
	 * Up
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

		if ($mwdb->tableHasField('session', 'params'))
		{
			$query = "ALTER TABLE `session` DROP `params`;";
			$mwdb->setQuery($query);
			$mwdb->query();
		}
		if ($mwdb->tableHasField('session', 'zone_id'))
		{
			$query = "ALTER TABLE `session` DROP `zone_id`;";
			$mwdb->setQuery($query);
			$mwdb->query();
		}
	}
}
