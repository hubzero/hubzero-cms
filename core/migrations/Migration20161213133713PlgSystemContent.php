<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

/**
 * Migration script for enabling the system content plugin
 **/
class Migration20161213133713PlgSystemContent extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('system', 'content');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->disablePlugin('system', 'content');
	}
}
