<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding the recurrence repeat_type option to the
 * incremental registration popover (0 = repeat indefinitely, 1 = stop
 * prompting once the recurrence list is exhausted).
 **/
class Migration20260629000000ModIncrementalRegistration extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__incremental_registration_options')
		 && !$this->db->tableHasField('#__incremental_registration_options', 'repeat_type'))
		{
			$query = "ALTER TABLE `#__incremental_registration_options` ADD COLUMN `repeat_type` TINYINT(1) NOT NULL DEFAULT 0";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__incremental_registration_options')
		 && $this->db->tableHasField('#__incremental_registration_options', 'repeat_type'))
		{
			$query = "ALTER TABLE `#__incremental_registration_options` DROP COLUMN `repeat_type`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
