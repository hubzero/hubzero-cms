<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for enabling the system content plugin
 **/
class Migration20161213133713PlgSystemContent extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('system', 'content');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->disablePlugin('system', 'content');
	}
}
