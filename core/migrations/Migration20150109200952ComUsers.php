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
 * Migration script for adding unique constraint to users.username field
 **/
class Migration20150109200952ComUsers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__users')
		 && $this->db->tableHasField('#__users', 'username')
		 && !$this->db->tableHasKey('#__users', 'uidx_username'))
		{
			$query = "ALTER TABLE `#__users` ADD CONSTRAINT UNIQUE KEY uidx_username(`username`)";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__users') && $this->db->tableHasKey('#__users', 'uidx_username'))
		{
			$query = "ALTER TABLE `#__users` DROP KEY uidx_username";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
