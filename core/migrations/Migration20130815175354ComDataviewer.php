<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

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