<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

/**
 * Migration script for ...
 **/
class Migration20240716193359ComPublications extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__publication_authors') && !$this->db->tableHasField('#__publication_authors', 'orcid'))
		{
			$query = "ALTER TABLE `#__publication_authors` ADD COLUMN `orcid` VARCHAR(50) NULL DEFAULT NULL";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__publication_authors') && $this->db->tableHasField('#__publication_authors', 'orcid'))
		{
			$query = "ALTER TABLE `#__publication_authors` DROP COLUMN `orcid`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
