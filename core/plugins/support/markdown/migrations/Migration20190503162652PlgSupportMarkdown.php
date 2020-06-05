<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing Markdown parser for support comments
 **/
class Migration20190503162652PlgSupportMarkdown extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('support', 'markdown', 0);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('support', 'markdown');
	}
}
