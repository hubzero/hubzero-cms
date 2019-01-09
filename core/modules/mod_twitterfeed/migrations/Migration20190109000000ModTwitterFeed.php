<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing twitterfeed module
 **/
class Migration20190109000000ModTwitterFeed extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_twitterfeed');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_twitterfeed');
	}
}
