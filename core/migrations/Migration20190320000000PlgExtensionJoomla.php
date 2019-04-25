<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for removing plg_extension_joomla plugin
 **/
class Migration20190320000000PlgExtensionJoomla extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->deletePluginEntry('extension', 'joomla');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->addPluginEntry('extension', 'joomla', 0);
	}
}
