<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing articles_archive module
 **/
class Migration20190109000000ModArticlesArchive extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_articles_archive');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_articles_archive');
	}
}
