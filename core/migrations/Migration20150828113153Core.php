<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for removing duplicate plugin entries while retaining proper parameters
 **/
class Migration201508281131531Core extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__extensions'))
		{
			$query = "SELECT MIN(extension_id) AS min, MAX(extension_id) as max, folder, element FROM `#__extensions` WHERE type='plugin' GROUP BY folder, element HAVING COUNT(*) > 1;";

			$this->db->setQuery($query);

			if ($results = $this->db->loadObjectList())
			{
				foreach ($results as $result)
				{
					if (empty($result) || empty($result->min) || empty($result->max) || empty($result->folder) || empty($result->element))
					{
						continue;
					}

					$query = "UPDATE `#__extensions` AS e1, `#__extensions` e2 SET e1.params = e2.params WHERE e1.extension_id = " . $this->db->quote($result->min) . " AND e2.extension_id = " . $this->db->quote($result->max) . ";";
					$this->db->setQuery($query);
					$this->db->query();

					$query = "DELETE FROM `#__extensions` WHERE folder = " . $this->db->quote($result->folder) . " AND element = " . $this->db->quote($result->element) . " AND extension_id != " . $this->db->quote($result->min);
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

	}
}
