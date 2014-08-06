<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for ...
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