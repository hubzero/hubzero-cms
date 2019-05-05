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
 * Migration to add id and primary key to #__events_config table
 **/
class Migration20180924164848ComEvents extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__events_config'))
		{
			if (!$this->db->tableHasField('#__events_config', 'id'))
			{
				$query = "ALTER TABLE `#__events_config` ADD `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST";
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
		if ($this->db->tableExists('#__events_config'))
		{
			if ($this->db->tableHasField('#__events_config', 'id'))
			{
				$query = "ALTER TABLE `#__events_config` DROP `id`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
