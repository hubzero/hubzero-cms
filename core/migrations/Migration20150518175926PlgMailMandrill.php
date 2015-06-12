<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding mandrill mail plugin
 **/
class Migration20150518175926PlgMailMandrill extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('mail', 'mandrill', 0);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('mail', 'mandrill');
	}
}