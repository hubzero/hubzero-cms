<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding developer component
 **/
class Migration20150609125958ComDeveloper extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addComponentEntry('developer');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteComponentEntry('developer');
	}
}