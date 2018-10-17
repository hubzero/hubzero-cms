<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding com_fmns component
 **/
class Migration20180808000000ComDrwho extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addComponentEntry('drwho');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteComponentEntry('drwho');
	}
}
