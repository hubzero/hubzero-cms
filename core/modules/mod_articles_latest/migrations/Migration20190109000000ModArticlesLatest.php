<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing articles_latest module
 **/
class Migration20190109000000ModArticlesLatest extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_articles_latest');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_articles_latest');
	}
}
