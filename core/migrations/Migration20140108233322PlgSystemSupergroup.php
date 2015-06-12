<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding super group system plugin
 **/
class Migration20140108233322PlgSystemSupergroup extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('system','supergroup');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('system','supergroup');
	}
}