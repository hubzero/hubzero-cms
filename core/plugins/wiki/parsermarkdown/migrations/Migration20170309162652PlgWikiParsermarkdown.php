<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

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
		$this->addPluginEntry('wiki', 'parsermarkdown', 0);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('wiki', 'parsermarkdown');
	}
}
