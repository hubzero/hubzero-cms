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
 * Migration script for adding Editors - Codemirror plugin
 **/
class Migration20170831000000PlgEditorsCodemirror extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('editors', 'codemirror', 0);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('editors', 'codemirror');
	}
}
