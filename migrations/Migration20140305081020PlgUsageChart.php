<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for deleting usage chart plugin
 **/
class Migration20140305081020PlgUsageChart extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->deletePluginEntry('usage', 'chart');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->addPluginEntry('usage', 'chart', 0);
	}
}