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
 * Migration script for joomla 2.5.28 update
 **/
class Migration20150109180705ComUsers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__user_profiles') && $this->db->tableHasField('#__user_profiles', 'profile_value'))
		{
			$query = "ALTER TABLE `#__user_profiles` CHANGE `profile_value` `profile_value` TEXT NOT NULL";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__user_profiles') && $this->db->tableHasField('#__user_profiles', 'profile_value'))
		{
			$query = "ALTER TABLE `#__user_profiles` CHANGE `profile_value` `profile_value` VARCHAR(255) NOT NULL";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
