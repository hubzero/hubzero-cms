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
 * Migration script for adding scope_id column to #__citations table
 **/
class Migration20141201151300ComCitations extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		// Checks whether table exists and if the 'scope' field already exists
		if ($this->db->tableExists('#__citations') && !$this->db->tableHasField('#__citations', 'scope_id'))
		{
			$query = "ALTER TABLE `#__citations` ADD COLUMN `scope_id` VARCHAR(45) NULL DEFAULT NULL;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		// Checks to see if field exists and removes it
		if ($this->db->tableExists('#__citations') && $this->db->tableHasField('#__citations', 'scope_id'))
		{
			$query = "ALTER TABLE `#__citations` DROP COLUMN `scope_id`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
