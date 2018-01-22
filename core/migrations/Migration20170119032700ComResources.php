<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script to rename production_collections primary key
 **/
class Migration20170119032700ComResources extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__resources'))
		{
			$query = "ALTER TABLE `#__resources` ADD COLUMN `license` CHAR(255);";

			$this->db->setQuery("SELECT `id`, `params` FROM `#__resources`;");
			$rows = $this->db->loadObjectList();
			foreach ($rows as $row)
			{
				$license = NULL;
				if ($row->params)
				{
					$json = json_decode($row->params);
					if (isset($json->{'license'}))
					{
						$license = $json->{'license'};
					}
				}
				if ($query)
				{
					$query .= "UPDATE `#__resources` SET `license`='$license' WHERE `id`='$row->id';";
				}
			}
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__resources'))
		{
			$query = "ALTER TABLE `#__resources` DROP COLUMN `license`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
