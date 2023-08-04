<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

defined('_HZEXEC_') or die();

/**
 * Migration script for ...
 **/
class Migration20220904223108ComPublications extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__publication_versions') && !$this->db->tableHasField('#__publication_versions', 'unpublished_reason'))
		{
			$query = "ALTER TABLE `#__publication_versions` ADD COLUMN `unpublished_reason` TEXT DEFAULT NULL";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__publication_versions') && !$this->db->tableHasField('#__publication_versions', 'unpublished_reason'))
		{
			$query = "ALTER TABLE `#__publication_versions` DROP COLUMN `unpublished_reason`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
