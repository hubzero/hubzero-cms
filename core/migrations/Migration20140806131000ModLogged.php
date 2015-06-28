<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for remove old mod_logged module
 **/
class Migration20140806131000ModLogged extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->deleteModuleEntry('mod_logged');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->addModuleEntry('mod_logged', 1, '', 1);
	}
}