<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

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
