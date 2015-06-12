<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

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