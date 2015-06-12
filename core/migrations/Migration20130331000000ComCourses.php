<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding faq, store, and related plugins
 **/
class Migration20130331000000ComCourses extends Base
{
	public function up()
	{
		$this->addPluginEntry('courses', 'faq');
		$this->addPluginEntry('courses', 'related');
		$this->addPluginEntry('courses', 'store');
	}

	public function down()
	{
		$this->deletePluginEntry('courses', 'faq');
		$this->deletePluginEntry('courses', 'related');
		$this->deletePluginEntry('courses', 'store');
	}
}