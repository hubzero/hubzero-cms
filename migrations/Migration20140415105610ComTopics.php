<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for deleting topics component entry
 **/
class Migration20140415105610ComTopics extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->deleteComponentEntry('topics');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->addComponentEntry('topics');
	}
}