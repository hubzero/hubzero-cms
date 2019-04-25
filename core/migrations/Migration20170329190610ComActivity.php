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
 * Migration script for adding Activity component entry
 **/
class Migration20170329190610ComActivity extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addComponentEntry('activity');

		$this->db->setQuery("UPDATE `#__extensions` SET `protected`=1 WHERE `element`='com_activity'");
		$this->db->query();
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteComponentEntry('activity');
	}
}
