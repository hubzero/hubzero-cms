<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding content format plugins
 **/
class Migration20140207091831PlgContent extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('content', 'formatwiki', 1, '{"applyFormat":"1","convertFormat":"0"}');
		$this->addPluginEntry('content', 'formathtml', 0);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('content', 'formatwiki');
		$this->deletePluginEntry('content', 'formathtml');
	}
}