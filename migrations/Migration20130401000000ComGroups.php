<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class Migration20130401000000ComGroups extends Hubzero_Migration
{
	protected static function up($db)
	{
		self::deletePluginEntry('groups', 'userenrollment');
	}
}