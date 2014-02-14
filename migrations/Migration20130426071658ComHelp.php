<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

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