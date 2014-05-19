<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding feedaggregator component entry
 **/
class Migration20140311160400ComFeedaggregator extends Base
{
	public function up()
	{
		$this->addComponentEntry('Feedaggregator');
	}

	public function down()
	{
		$this->deleteComponentEntry('Feedaggregator');
	}
}