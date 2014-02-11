<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for ...
 **/
class Migration20140211083120PlgEditorsWikiwyg extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		self::addPluginEntry('editors','wikiwyg');
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		self::deletePluginEntry('editors','wikiwyg');
	}
}