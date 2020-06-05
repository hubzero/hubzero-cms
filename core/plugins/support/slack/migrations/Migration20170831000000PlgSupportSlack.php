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
 * Migration script for adding Support - Slack plugin
 **/
class Migration20170831000000PlgSupportSlack extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('support', 'slack', 0);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('support', 'slack');
	}
}
