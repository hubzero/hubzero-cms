<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class Migration20130201000000PlgUserConstantcontact extends Hubzero_Migration
{
	protected static function up($db)
	{
		self::addPluginEntry('user', 'constantcontact');
	}

	protected function down($db)
	{
		self::deletePluginEntry('user', 'constantcontact');
	}
}
