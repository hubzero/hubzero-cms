<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding column to track when activity should be reported as anonymous
 **/
class Migration20160818061637Core extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableHasField('#__activity_logs', 'anonymous'))
		{
			$query = "ALTER TABLE `#__activity_logs` ADD `anonymous` TINYINT(2)  UNSIGNED  NOT NULL  DEFAULT '0';";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableHasField('#__activity_logs', 'anonymous'))
		{
			$query = "ALTER TABLE `#__activity_logs` DROP COLUMN `anonymous`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
