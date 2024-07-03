<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

/**
 * Migration script for column orcid_work_put_code in table #__publication_authors
 **/
class Migration20240502154413ComPublications extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__publication_authors') && !$this->db->tableHasField('#__publication_authors', 'orcid_work_put_code'))
		{
			$query = "ALTER TABLE `#__publication_authors` ADD COLUMN `orcid_work_put_code` VARCHAR(50) NULL DEFAULT NULL";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__publication_authors') && $this->db->tableHasField('#__publication_authors', 'orcid_work_put_code'))
		{
			$query = "ALTER TABLE `#__publication_authors` DROP COLUMN `orcid_work_put_code`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
