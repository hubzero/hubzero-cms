<?php

use Hubzero\Content\Migration;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for ...
 **/
class Migration20140207091831PlgContent extends Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		self::addPluginEntry('content', 'formatwiki', 1, '{"applyFormat":"1","convertFormat":"0"}');
		self::addPluginEntry('content', 'formathtml', 0);
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		self::deletePluginEntry('content', 'formatwiki');
		self::deletePluginEntry('content', 'formathtml');
	}
}