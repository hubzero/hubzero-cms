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
 * Migration script for adding asset_id field to time hubs table
 **/
class Migration20140807200026ComTime extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__time_hubs') && !$this->db->tableHasField('#__time_hubs', 'asset_id'))
		{
			$query = "ALTER TABLE `#__time_hubs` ADD `asset_id` INT(11) NULL DEFAULT NULL AFTER `notes`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__time_hubs') && $this->db->tableHasField('#__time_hubs', 'asset_id'))
		{
			$query = "ALTER TABLE `#__time_hubs` DROP `asset_id`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
