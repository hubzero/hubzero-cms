<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class Migration20130201000000PlgUserConstantcontact extends Hubzero_Migration
{
	protected static function up($db)
	{
		$query = "INSERT INTO `#__plugins` (`name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`)
					SELECT 'User - Constant Contact', 'constantcontact', 'user', 0, 0, 1, 0, 0, 0, '0000-00-00 00:00:00', ''
					FROM DUAL WHERE NOT EXISTS (SELECT `name` FROM `#__plugins` WHERE name = 'User - Constant Contact');";

		$db->setQuery($query);
		$db->query();
	}

	protected function down($db)
	{
		$query = "DELETE FROM `#__plugins` WHERE folder='user' AND element='constantcontact';";

		$db->setQuery($query);
		$db->query();
	}
}
