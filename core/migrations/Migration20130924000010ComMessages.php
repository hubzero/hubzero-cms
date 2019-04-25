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
 * Migration script for messages table changes
 **/
class Migration20130924000010ComMessages extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query = "ALTER TABLE `#__messages` ENGINE = MYISAM  ;\n";
		$query .= "ALTER TABLE `#__messages_cfg` ENGINE = MYISAM ;";
		$this->db->setQuery($query);
		$this->db->query();

		if ($this->db->tableHasField('#__messages', 'subject'))
		{
			$query = "ALTER TABLE `#__messages` CHANGE `subject` `subject` varchar(255) NOT NULL DEFAULT '';";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasField('#__messages', 'state'))
		{
			$query = "ALTER TABLE `#__messages` CHANGE `state` `state` tinyint(1) NOT NULL DEFAULT '0';";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasField('#__messages', 'priority'))
		{
			$query = "ALTER TABLE `#__messages` CHANGE `priority` `priority` tinyint(1) UNSIGNED NOT NULL DEFAULT '0';";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasField('#__messages', 'folder_id'))
		{
			$query = "ALTER TABLE `#__messages` CHANGE `folder_id` `folder_id` tinyint(3) UNSIGNED NOT NULL DEFAULT '0';";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
