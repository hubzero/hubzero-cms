<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding password expiration/rules route plugin
 **/
class Migration20150626173412PlgSystemPassword extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('system', 'password');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('system', 'password');
	}
}