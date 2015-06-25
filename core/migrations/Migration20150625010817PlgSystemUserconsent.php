<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding fisma user consent plugin
 **/
class Migration20150625010817PlgSystemUserconsent extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('system', 'userconsent', 0);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('system', 'userconsent');
	}
}