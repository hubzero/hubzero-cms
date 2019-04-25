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
 * Migration script for delete rogue entry to nonexistent plugin
 **/
class Migration20141029174920PlgUserContactcreator extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->deletePluginEntry('user', 'contactcreator');
	}
}
