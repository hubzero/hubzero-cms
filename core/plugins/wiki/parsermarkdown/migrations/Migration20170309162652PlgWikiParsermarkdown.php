<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing Markdown parser for wiki
 **/
class Migration20170309162652PlgWikiParsermarkdown extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('wiki', 'parsermarkdown');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('wiki', 'parsermarkdown');
	}
}
