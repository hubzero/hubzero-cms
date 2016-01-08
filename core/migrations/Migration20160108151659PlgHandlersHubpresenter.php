<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding hubpresenter file handler
 **/
class Migration20160108151659PlgHandlersHubpresenter extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('handlers', 'hubpresenter');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('handlers', 'hubpresenter');
	}
}