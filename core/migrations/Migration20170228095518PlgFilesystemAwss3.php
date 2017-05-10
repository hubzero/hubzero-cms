<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding GitHub filesystem plugin
 **/
class Migration20170228095518PlgFilesystemAwss3 extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('filesystem', 'awss3');

		if ($this->db->tableExists('#__projects_connection_providers'))
		{
			$query = "SELECT * FROM `#__projects_connection_providers` WHERE `alias`='awss3'";
			$this->db->setQuery($query);
			$results = $this->db->loadObjectList();

			if (count($results) < 1)
			{
				$query = "INSERT INTO `#__projects_connection_providers` (`alias`, `name`) VALUES ('awss3','AWS S3')";
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
		$this->deletePluginEntry('filesystem', 'awss3');

		if ($this->db->tableExists('#__projects_connection_providers'))
		{
			$query = "SELECT * FROM `#__projects_connection_providers` WHERE `alias`='awss3'";
			$this->db->setQuery($query);
			$results = $this->db->loadObjectList();

			// Find and delete all connections using this connection
			foreach ($results as $result)
			{
				$query = "DELETE FROM `#__projects_connections` WHERE `provider_id`=" . $this->db->quote($result->id);
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (count($results) > 0)
			{
				$query = "DELETE FROM `#__projects_connection_providers` WHERE `alias`='awss3'";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
