<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding pdf file handler
 **/
class Migration20160108151435PlgHandlersPdf extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('handlers', 'pdf');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('handlers', 'pdf');
	}
}