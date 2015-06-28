<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding help component
 **/
class Migration20130426071658ComHelp extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addComponentEntry('Help');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteComponentEntry('Help');
	}
}