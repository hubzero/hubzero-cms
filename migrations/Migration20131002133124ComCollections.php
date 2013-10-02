<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for collection plugin entries
 **/
class Migration20131002133124ComCollections extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		self::addPluginEntry('members', 'collections', 0);
		self::addPluginEntry('groups', 'collections', 0);
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		self::deletePluginEntry('members', 'collections');
		self::deletePluginEntry('groups', 'collections');
	}
}