<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding Captcha - Recaptcha plugin
 **/
class Migration20170831000000PlgCaptchaRecaptcha extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('captcha', 'recaptcha', 0);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('captcha', 'recaptcha');
	}
}
