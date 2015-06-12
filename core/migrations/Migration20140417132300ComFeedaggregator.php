<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding feedaggregator entry in disabled state
 **/
class Migration20140417132300ComFeedaggregator extends Base
{
	public function up()
	{
		$this->deleteComponentEntry('feedaggregator');
		$this->addComponentEntry('feedaggregator',NULL,1,'',false);
	}

	public function down()
	{
		$this->deleteComponentEntry('feedaggregator');
	}
}