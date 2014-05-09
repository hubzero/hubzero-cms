<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for for adding time plugin for support
 **/
class Migration20140505160720PlgSupportTime extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('support','time', 0);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('support','time');
	}
}