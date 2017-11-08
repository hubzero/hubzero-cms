<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding Captcha - Image plugin
 **/
class Migration20170831000000PlgCaptchaImage extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('captcha', 'image');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('captcha', 'image');
	}
}
