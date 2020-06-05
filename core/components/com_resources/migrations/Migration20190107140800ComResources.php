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
 * Migration script to add a doi_shoulder column
 **/
class Migration20190107140800ComResources extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__doi_mapping'))
		{
			if (!$this->db->tableHasField('#__doi_mapping', 'doi_shoulder'))
			{
				$query = "ALTER TABLE `#__doi_mapping` ADD COLUMN `doi_shoulder` VARCHAR(50);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__doi_mapping', 'idx_doi_shoulder'))
			{
				$query = "ALTER TABLE `#__doi_mapping` ADD INDEX `idx_doi_shoulder` (`doi_shoulder`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			$this->db->setQuery("SELECT `params` FROM `#__extensions` WHERE `element`='com_tools';");
			$row = $this->db->loadResult();

			$params = json_decode($row);

			if (isset($params->doi_shoulder) && $params->doi_shoulder)
			{
				$query = "UPDATE `#__doi_mapping` SET `doi_shoulder`=" . $this->db->quote($params->doi_shoulder) . " WHERE `doi_shoulder` IS NULL";
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
			if ($this->db->tableHasKey('#__doi_mapping', 'idx_doi_shoulder'))
			{
				$query = "ALTER TABLE `#__doi_mapping` DROP KEY `idx_doi_shoulder'";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__doi_mapping', 'doi_shoulder'))
			{
				$query = "ALTER TABLE `#__doi_mapping` DROP COLUMN `doi_shoulder`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
