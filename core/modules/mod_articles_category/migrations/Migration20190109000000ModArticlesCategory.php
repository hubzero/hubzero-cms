<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing articles_category module
 **/
class Migration20190109000000ModArticlesCategory extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_articles_category');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_articles_category');
	}
}
