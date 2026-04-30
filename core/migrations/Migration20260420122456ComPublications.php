<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of Purdue University.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

/**
 * Migration script for column department in table #__publication_authors
 **/
class Migration20260420122456ComPublications extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__publication_authors') && !$this->db->tableHasField('#__publication_authors', 'department'))
		{
			$query = "ALTER TABLE `#__publication_authors` ADD COLUMN `department` VARCHAR(255) NULL DEFAULT NULL";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__publication_authors') && $this->db->tableHasField('#__publication_authors', 'department'))
		{
			$query = "ALTER TABLE `#__publication_authors` DROP COLUMN `department`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
