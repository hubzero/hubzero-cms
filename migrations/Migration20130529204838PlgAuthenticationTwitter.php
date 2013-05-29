<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding twitter authentication plugin
 **/
class Migration20130529204838PlgAuthenticationTwitter extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		$query = "INSERT INTO `#__plugins` (`name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`)
					SELECT 'Authentication - Twitter', 'twitter', 'authentication', 0, 8, 0, 0, 0, 0, '0000-00-00 00:00:00', ''
					FROM DUAL WHERE NOT EXISTS (SELECT `name` FROM `#__plugins` WHERE name = 'Authentication - Twitter');";

		$db->setQuery($query);
		$db->query();
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		$query = "DELETE FROM `#__plugins` WHERE folder='authentication' AND element='twitter';";

		$db->setQuery($query);
		$db->query();
	}
}