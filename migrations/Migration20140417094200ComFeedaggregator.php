<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class Migration20140417094200ComFeedaggregator extends Base
{
	public function up()
	{
		$this->deleteComponentEntry('Feedaggregator');
		$this->addComponentEntry('Feedaggregator', 'menu-item=0');	
	}

	public function down()
	{
		$this->deleteComponentEntry('Feedaggregator');
	}
}