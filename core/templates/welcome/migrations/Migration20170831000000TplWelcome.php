<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding entry for Template - Welcome plugin
 **/
class Migration20170831000000TplWelcome extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addTemplateEntry('welcome', 'welcome', 0, 1, 0, null, 1);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteTemplateEntry('welcome', 0);
	}
}
