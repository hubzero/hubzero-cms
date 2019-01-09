<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing articles_news module
 **/
class Migration20190109000000ModArticlesNews extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_articles_news');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_articles_news');
	}
}
