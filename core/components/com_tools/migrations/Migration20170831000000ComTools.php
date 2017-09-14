<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding component entry for com_tools
 **/
class Migration20170831000000ComTools extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addComponentEntry('tools');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteComponentEntry('tools');
	}
}
