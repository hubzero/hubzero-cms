<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding ckeditor plugin entry
 **/
class Migration20140108233320PlgEditorsCkeditor extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('editors','ckeditor');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('editors','ckeditor');
	}
}