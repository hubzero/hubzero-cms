<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding projects links plugin
 **/
class Migration20140211154400PlgProjectsLinks extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		self::addPluginEntry('projects', 'links');
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		self::deletePluginEntry('projects', 'links');
	}
}