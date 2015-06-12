<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for addind cart component entry
 **/
class Migration20130821164628ComCart extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addComponentEntry('Cart');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteComponentEntry('Cart');
	}
}