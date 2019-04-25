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
 * Migration script for deleting groups userenrollment plugin
 **/
class Migration20130401000000ComGroups extends Base
{
	public function up()
	{
		$this->deletePluginEntry('groups', 'userenrollment');
	}
}
