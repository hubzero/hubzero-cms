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
 * Migration script for adding Hubzero - Comments plugin
 **/
class Migration20170831000000PlgHubzeroComments extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('hubzero', 'comments');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('hubzero', 'comments');
	}
}
