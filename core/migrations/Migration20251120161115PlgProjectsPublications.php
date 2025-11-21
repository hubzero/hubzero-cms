<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

/**
 * Migration script for column size in publication version table
 **/
class Migration20251120161115PlgProjectsPublications extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__publication_versions') && !$this->db->tableHasField('#__publication_versions', 'datasize'))
		{
			$query = "ALTER TABLE `#__publication_versions` ADD COLUMN `datasize` BIGINT UNSIGNED";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__publication_versions') && $this->db->tableHasField('#__publication_versions', 'datasize'))
		{
			$query = "ALTER TABLE `#__publication_versions` DROP COLUMN `datasize`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
