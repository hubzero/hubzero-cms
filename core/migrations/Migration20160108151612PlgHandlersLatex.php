<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding latex file handler
 **/
class Migration20160108151612PlgHandlersLatex extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('handlers', 'latex');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('handlers', 'latex');
	}
}