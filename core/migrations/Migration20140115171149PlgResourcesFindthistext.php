<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding resources findthistext plugin
 **/
class Migration20140115171149PlgResourcesFindthistext extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('resources','findthistext');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('resources','findthistext');
	}
}