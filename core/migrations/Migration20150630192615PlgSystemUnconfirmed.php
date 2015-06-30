<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding system plugin verifying email confirmation status
 **/
class Migration20150630192615PlgSystemUnconfirmed extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('system', 'unconfirmed');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('system', 'unconfirmed');
	}
}