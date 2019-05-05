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
 * Migration script for fixing up members badges table
 **/
class Migration20130814201250ComCourses extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableHasField('#__courses_member_badges', 'claimed') && !$this->db->tableHasField('#__courses_member_badges', 'action'))
		{
			$query = "ALTER TABLE `#__courses_member_badges` CHANGE `claimed` `action` VARCHAR(255) NULL DEFAULT NULL;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasField('#__courses_member_badges', 'claimed_on') && !$this->db->tableHasField('#__courses_member_badges', 'action_on'))
		{
			$query = "ALTER TABLE `#__courses_member_badges` CHANGE `claimed_on` `action_on` DATETIME NULL  DEFAULT NULL;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if (!$this->db->tableHasField('#__courses_member_badges', 'claimed') && $this->db->tableHasField('#__courses_member_badges', 'action'))
		{
			$query = "ALTER TABLE `#__courses_member_badges` CHANGE `action` `claimed` INT(1) NULL DEFAULT NULL;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if (!$this->db->tableHasField('#__courses_member_badges', 'claimed_on') && $this->db->tableHasField('#__courses_member_badges', 'action_on'))
		{
			$query = "ALTER TABLE `#__courses_member_badges` CHANGE `action_on` `claimed_on` DATETIME NULL  DEFAULT NULL;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
