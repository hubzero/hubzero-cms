<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding auth factors authy plugin
 **/
class Migration20150511160714PlgAuthfactorsAuthy extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('authfactors', 'authy', 0);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('authfactors', 'authy');
	}
}