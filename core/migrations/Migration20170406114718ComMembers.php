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
 * Migration script to add `default_value` column to profile fields table
 **/
class Migration20170406114718ComMembers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__user_profile_fields'))
		{
			if (!$this->db->tableHasField('#__user_profile_fields', 'default_value'))
			{
				$query = "ALTER TABLE `#__user_profile_fields` ADD `default_value` varchar(255)";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__user_profile_fields'))
		{
			if ($this->db->tableHasField('#__user_profile_fields', 'default_value'))
			{
				$query = "ALTER TABLE `#__user_profile_fields` DROP `default_value`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
