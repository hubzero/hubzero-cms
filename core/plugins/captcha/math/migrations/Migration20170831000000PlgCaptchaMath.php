<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding Captcha - Math plugin
 **/
class Migration20170831000000PlgCaptchaMath extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('captcha', 'math', 0);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('captcha', 'math');
	}
}
