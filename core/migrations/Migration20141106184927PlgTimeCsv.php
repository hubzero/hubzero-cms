<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for csv time plugin
 **/
class Migration20141106184927PlgTimeCsv extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('time', 'csv', 0);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('time', 'csv');
	}
}