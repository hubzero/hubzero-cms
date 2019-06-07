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
 * Migration script for adding custom fields to citations
 **/
class Migration20150108140000ComCitations extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		//checks whether table exists and if the 'scope' field already exists
		if ($this->db->tableExists('#__citations') && !$this->db->tableHasField('#__citations', 'custom1'))
		{
			$query = "ALTER TABLE `#__citations`
			ADD COLUMN `custom1` TEXT NULL DEFAULT NULL,
			ADD COLUMN `custom2` TEXT NULL DEFAULT NULL,
			ADD COLUMN `custom3` VARCHAR(45) NULL DEFAULT NULL,
			ADD COLUMN `custom4` VARCHAR(45) NULL DEFAULT NULL";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		// Checks to see if gid field exists and removes it
		if ($this->db->tableExists('#__citations') && $this->db->tableHasField('#__citations', 'custom1'))
		{
			$query = "ALTER TABLE `#__citations`
			DROP COLUMN `custom1`,
			DROP COLUMN `custom2`,
			DROP COLUMN `custom3`,
			DROP COLUMN `custom4`";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
