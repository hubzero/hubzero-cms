<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing articles_categories module
 **/
class Migration20190109000000ModArticlesCategories extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_articles_categories');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_articles_categories');
	}
}
