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
 * Migration script for adding 'modified' and 'modified_by' columns to extensions table
 **/
class Migration20180321201800Core extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__extensions'))
		{
			if (!$this->db->tableHasField('#__extensions', 'modified'))
			{
				$query = "ALTER TABLE `#__extensions` ADD `modified` datetime DEFAULT NULL";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasField('#__extensions', 'modified_by'))
			{
				$query = "ALTER TABLE `#__extensions` ADD `modified_by` int(11) NOT NULL DEFAULT '0';";
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
		if ($this->db->tableExists('#__extensions'))
		{
			if ($this->db->tableHasField('#__extensions', 'modified'))
			{
				$query = "ALTER TABLE `#__extensions` DROP COLUMN `modified`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__extensions', 'modified_by'))
			{
				$query = "ALTER TABLE `#__extensions` DROP COLUMN `modified_by`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
