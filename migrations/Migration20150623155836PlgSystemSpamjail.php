<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding spamjail plugin
 **/
class Migration20150623155836PlgSystemSpamjail extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('system', 'spamjail');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('system', 'spamjail');
	}
}