<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding com_fmns component
 **/
class Migration20180712000000ComFmns extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addComponentEntry('fmns');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteComponentEntry('fmns');
	}
}
