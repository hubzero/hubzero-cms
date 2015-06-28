<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding update component entry
 **/
class Migration20140520200100ComUpdate extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addComponentEntry('Update', 'com_update', 1, '', false);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteComponentEntry('Update');
	}
}
