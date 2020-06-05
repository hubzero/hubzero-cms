<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding Wiki - Parserdefault plugin
 **/
class Migration20170831000000PlgWikiParserdefault extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('wiki', 'parserdefault');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('wiki', 'parserdefault');
	}
}
