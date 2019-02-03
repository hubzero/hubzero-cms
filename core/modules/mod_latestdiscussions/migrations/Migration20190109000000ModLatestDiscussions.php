<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing Latest Discussions module
 **/
class Migration20190109000000ModLatestDiscussions extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_latestdiscussions');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_latestdiscussions');
	}
}
