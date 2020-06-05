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
 * Migration script for adding versionid and doi columns to doi_mapping table
 **/
class Migration20180529200400ComTools extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__doi_mapping'))
		{
			if (!$this->db->tableHasField('#__doi_mapping', 'versionid'))
			{
				$query = "ALTER TABLE `#__doi_mapping` ADD `versionid` INT(11)  NULL  DEFAULT '0'  AFTER `alias`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasField('#__doi_mapping', 'doi'))
			{
				$query = "ALTER TABLE `#__doi_mapping` ADD `doi` VARCHAR(50)  NULL  DEFAULT NULL  AFTER `versionid`;";
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
		if ($this->db->tableExists('#__doi_mapping'))
		{
			if ($this->db->tableHasField('#__doi_mapping', 'versionid'))
			{
				$query = "ALTER TABLE `#__doi_mapping` DROP COLUMN `versionid`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__doi_mapping', 'doi'))
			{
				$query = "ALTER TABLE `#__doi_mapping` DROP COLUMN `doi`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
