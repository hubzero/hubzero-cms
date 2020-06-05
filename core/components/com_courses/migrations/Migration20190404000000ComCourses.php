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
 * Migration script for add 'forked_from' column to `#__courses` table
 **/
class Migration20190404000000ComCourses extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__courses')
		 && !$this->db->tableHasField('#__courses', 'forked_from'))
		{
			$query = "ALTER TABLE `#__courses` ADD `forked_from` INT(11)  NOT NULL  DEFAULT '0'";

			$this->db->setQuery($query);
			$this->db->query();

			$this->log('Added column "forked_from" to `#__courses` table');
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__courses')
		 && $this->db->tableHasField('#__courses', 'forked_from'))
		{
			$query = "ALTER TABLE `#__coursesg` DROP COLUMN `forked_from`;";

			$this->db->setQuery($query);
			$this->db->query();

			$this->log('Removed column "forked_from" from `#__courses` table');
		}
	}
}
