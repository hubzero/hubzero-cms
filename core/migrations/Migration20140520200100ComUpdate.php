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
 * Migration script for adding update component entry
 **/
class Migration20140520200100ComUpdate extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addComponentEntry('Update', 'com_update', 1, '', false);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteComponentEntry('Update');
	}
}
