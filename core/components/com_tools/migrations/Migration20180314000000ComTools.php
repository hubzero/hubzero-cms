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
 * Migration script for adding a primary key to the DOI table
 **/
class Migration20180314000000ComTools extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__doi_mapping'))
		{
			if (!$this->db->tableHasField('#__doi_mapping', 'id'))
			{
				$query = "ALTER TABLE `#__doi_mapping` ADD `id` INT(11) UNSIGNED  NOT NULL  AUTO_INCREMENT  PRIMARY KEY;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__doi_mapping', 'idx_rid'))
			{
				$query = "ALTER TABLE `#__doi_mapping` ADD INDEX `idx_rid` (`rid`)";
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
			if ($this->db->tableHasField('#__doi_mapping', 'id'))
			{
				$query = "ALTER TABLE `#__doi_mapping` DROP `id`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__doi_mapping', 'idx_rid'))
			{
				$query = "ALTER TABLE `#__doi_mapping` DROP KEY `idx_rid`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
