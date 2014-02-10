<?php

use Hubzero\Content\Migration;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for ...
 **/
class Migration20140110125436ComWrapper extends Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		$query = "SELECT `extension_id` FROM `#__extensions` WHERE `type`='component' AND `element`='com_wrapper';";

		$db->setQuery($query);

		if ($id = $db->loadResult())
		{
			self::deleteComponentEntry('wrapper');
		}
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		$query = "SELECT `extension_id` FROM `#__extensions` WHERE `type`='component' AND `element`='com_wrapper';";

		$db->setQuery($query);

		if (!($id = $db->loadResult()))
		{
			self::addComponentEntry('wrapper');
		}
	}
}