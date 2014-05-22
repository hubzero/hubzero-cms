<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for for adding antispam plugins for Akismet, Mollom, and SpamAssassin
 **/
class Migration20140522100120PlgContentAntispam extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('content','akismet', 0);
		$this->addPluginEntry('content','mollom', 0);
		$this->addPluginEntry('content','spamassassin', 0);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('content','akismet');
		$this->deletePluginEntry('content','mollom');
		$this->deletePluginEntry('content','spamassassin');
	}
}