<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for removing old mod_status module.
 **/
class Migration20140806144415ModStatus extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->deleteModuleEntry('mod_status');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->addModuleEntry('mod_status', 1, '', 1);
	}
}