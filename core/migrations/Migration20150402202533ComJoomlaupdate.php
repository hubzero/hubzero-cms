<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

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