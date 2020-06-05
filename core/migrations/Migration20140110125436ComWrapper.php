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
 * Migration script for deleting com_wrapper
 **/
class Migration20140110125436ComWrapper extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query = "SELECT `extension_id` FROM `#__extensions` WHERE `type`='component' AND `element`='com_wrapper';";

		$this->db->setQuery($query);

		if ($id = $this->db->loadResult())
		{
			$this->deleteComponentEntry('wrapper');
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$query = "SELECT `extension_id` FROM `#__extensions` WHERE `type`='component' AND `element`='com_wrapper';";

		$this->db->setQuery($query);

		if (!($id = $this->db->loadResult()))
		{
			$this->addComponentEntry('wrapper');
		}
	}
}
