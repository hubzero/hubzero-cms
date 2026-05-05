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
 * Migration script for adding User - US plugin
 **/
class Migration20260421000001PlgUserUs extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('user', 'us');

		if ($this->db->tableExists('#__xgroups'))
		{
			$this->db->setQuery(
				"INSERT IGNORE INTO `#__xgroups` (`cn`, `published`, `approved`, `type`, `join_policy`, `discoverability`)
				 VALUES ('location_us', 1, 1, 1, 3, 1)"
			);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__xgroups'))
		{
			$this->db->setQuery("DELETE FROM `#__xgroups` WHERE `cn` = 'location_us'");
			$this->db->query();
		}

		$this->deletePluginEntry('user', 'us');
	}
}
