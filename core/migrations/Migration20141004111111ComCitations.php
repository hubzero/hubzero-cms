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
 * Migration script for adding scope and scope_id to #__citations_secondary table
 **/
class Migration20141004111111ComCitations extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		// Checks whether table exists and if the 'scope' field already exists
		if ($this->db->tableExists('#__citations_secondary') && !$this->db->tableHasField('#__citations_secondary', 'scope'))
		{
			$query = "ALTER TABLE `#__citations_secondary`
			ADD COLUMN `scope` VARCHAR(250) NULL DEFAULT NULL,
			ADD COLUMN `scope_id` INT(11) NULL DEFAULT NULL;";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		// Checks to see if fieldd exists and removes it
		if ($this->db->tableExists('#__citations_secondary') && $this->db->tableHasField('#__citations_secondary', 'scope'))
		{
			$query = "ALTER TABLE `#__citations_secondary`
			DROP COLUMN `scope`,
			DROP COLUMN `scope_id`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
