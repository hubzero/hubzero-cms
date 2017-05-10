<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for removing unused com_admin component
 **/
class Migration20170313151515ComAdmin extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->deleteComponentEntry('admin');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->addComponentEntry('admin');
	}
}
