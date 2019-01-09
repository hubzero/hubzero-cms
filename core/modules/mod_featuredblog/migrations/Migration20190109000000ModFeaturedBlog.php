<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing featuredblog module
 **/
class Migration20190109000000ModFeaturedBlog extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_featuredblog');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_featuredblog');
	}
}
