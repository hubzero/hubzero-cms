<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding com_dataviewer
 **/
class Migration20130815175354ComDataviewer extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$params = array(
			"record_display_limit"   => "10",
			"processing_mode_switch" => "0",
			"proc_switch_threshold"  => "25000",
			"acl_users"              => "",
			"acl_groups"             => ""
		);

		$this->addComponentEntry('Dataviewer', 'com_dataviewer', 1, $params);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteComponentEntry('Dataviewer');
	}
}