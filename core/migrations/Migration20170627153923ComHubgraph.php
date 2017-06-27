<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for removing com_hubgraph
 **/
class Migration20170627153923ComHubgraph extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->deleteComponentEntry('hubgraph');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->addComponentEntry('hubgraph');
	}
}
