<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing feed_youtube module
 **/
class Migration20190109000000ModFeedYoutube extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_feed_youtube');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_feed_youtube');
	}
}
