<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Google Drive filesystem plugin
 **/
class Migration20170203204306PlgFilesystemGoogledrive extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('filesystem', 'googledrive');

		if ($this->db->tableExists('#__projects_connection_providers'))
		{
			$query = "SELECT * FROM `#__projects_connection_providers` WHERE `alias`='googledrive'";
			$this->db->setQuery($query);
			$results = $this->db->loadObjectList();

			if (count($results) < 1)
			{
				$query = "INSERT INTO `#__projects_connection_providers` (`alias`, `name`) VALUES ('googledrive','Google Drive')";
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
		$this->deletePluginEntry('filesystem', 'googledrive');

		if ($this->db->tableExists('#__projects_connection_providers'))
		{
			$query = "SELECT * FROM `#__projects_connection_providers` WHERE `alias`='googledrive'";
			$this->db->setQuery($query);
			$results = $this->db->loadObjectList();

			foreach ($results as $result)
			{
				$query = "DELETE FROM `#__projects_connections` WHERE `provider_id`=" . $this->db->quote($result->id);
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (count($results) > 0)
			{
				$query = "DELETE FROM `#__projects_connection_providers` WHERE `alias`='googledrive'";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
