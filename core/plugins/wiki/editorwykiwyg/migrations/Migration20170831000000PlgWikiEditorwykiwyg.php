<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding Wiki - Editorwykiwyg plugin
 **/
class Migration20170831000000PlgWikiEditorwykiwyg extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('wiki', 'editorwykiwyg');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('wiki', 'editorwykiwyg');
	}
}
