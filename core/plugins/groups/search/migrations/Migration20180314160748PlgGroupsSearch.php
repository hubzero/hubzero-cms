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
 * Migration script for adding Groups - Search plugin
 **/
class Migration20180314160748PlgGroupsSearch extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('groups', 'search');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('groups', 'search');
	}
}
