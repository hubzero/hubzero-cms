<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

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