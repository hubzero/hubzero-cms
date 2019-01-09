<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing articles_popular module
 **/
class Migration20190109000000ModArticlesPopular extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_articles_popular');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_articles_popular');
	}
}
