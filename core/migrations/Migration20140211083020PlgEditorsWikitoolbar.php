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
 * Migration script for adding wikitoolbar editor
 **/
class Migration20140211083020PlgEditorsWikitoolbar extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('editors', 'wikitoolbar');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('editors', 'wikitoolbar');
	}
}
