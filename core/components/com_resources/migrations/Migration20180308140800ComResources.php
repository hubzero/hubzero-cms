<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script to add a license column
 **/
class Migration20180308140800ComResources extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__resources') && !$this->db->tableHasField('#__resources', 'license'))
		{
			$query = "ALTER TABLE `#__resources` ADD COLUMN `license` CHAR(255);";
			$this->db->setQuery($query);
			$this->db->query();

			if (!$this->db->tableHasKey('#__resources', 'idx_license'))
			{
				$query = "ALTER TABLE `#__resources` ADD INDEX `idx_license` (`license`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			$this->db->setQuery("SELECT `id`, `params` FROM `#__resources` WHERE `standalone`=1;");
			$rows = $this->db->loadObjectList();

			foreach ($rows as $row)
			{
				$license = null;

				if ($row->params)
				{
					$json = json_decode($row->params);
					if (isset($json->license))
					{
						$license = $json->license;
					}
				}

				if ($license)
				{
					$query = "UPDATE `#__resources` SET `license`=" . $this->db->quote($license) . " WHERE `id`=" . $this->db->quote($row->id);
					$this->db->setQuery($query);
					$this->db->query();
				}
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__resources'))
		{
			if ($this->db->tableHasKey('#__resources', 'idx_license'))
			{
				$query = "ALTER TABLE `#__resources` DROP KEY `idx_license'";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__resources', 'license'))
			{
				$query = "ALTER TABLE `#__resources` DROP COLUMN `license`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
