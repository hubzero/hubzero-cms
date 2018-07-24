<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding Blog - Twitter plugin
 **/
class Migration20180722000000PlgBlogTwitter extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('blog', 'twitter');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('blog', 'twitter');
	}
}
