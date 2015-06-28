<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for removing com_joomlaupdate
 **/
class Migration20150402202533ComJoomlaupdate extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->deleteComponentEntry('joomlaupdate');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->addComponentEntry('joomlaupdate');
	}
}