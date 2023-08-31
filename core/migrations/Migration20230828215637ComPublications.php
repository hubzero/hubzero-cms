<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2023 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

/**
 * Migration script for ...
 **/
class Migration20230828215637ComPublications extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__publication_versions') && !$this->db->tableHasField('#__publication_versions', 'downloadable'))
		{
			$query = "ALTER TABLE `#__publication_versions` ADD COLUMN `downloadable` BOOL DEFAULT FALSE";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__publication_versions') && $this->db->tableHasField('#__publication_versions', 'downloadable'))
		{
			$query = "ALTER TABLE `#__publication_versions` DROP COLUMN `downloadable`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
