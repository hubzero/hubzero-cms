<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for ...
 **/
class Migration20130501120700PlgCronGroups extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		$query = "INSERT INTO `#__plugins` (`name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`)
		VALUES ('Cron - Groups', 'groups', 'cron', 0, 4, 1, 0, 0, 0, '0000-00-00 00:00:00', ''); \n";

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}
}