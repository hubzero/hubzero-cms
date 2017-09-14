<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding component entry for com_dataviewer
 **/
class Migration20170831000000ComDataviewer extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addComponentEntry('dataviewer');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteComponentEntry('dataviewer');
	}
}
