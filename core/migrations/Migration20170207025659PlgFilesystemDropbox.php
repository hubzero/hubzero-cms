<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for ...
 **/
class Migration20170207025659PlgFilesystemDropbox extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('filesystem', 'dropbox');
		if($this->db->tableExists('#__projects_connection_providers'))
		{
			$query = "SELECT * FROM `#__projects_connection_providers` WHERE `alias`='dropbox'";
			$this->db->setQuery($query);
			$results = $this->db->loadObjectList();
			if (count($results) < 1)
			{
				$query = "INSERT INTO `#__projects_connection_providers` (`alias`, `name`) VALUES ('dropbox','Dropbox')";
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
		$this->deletePluginEntry('filesystem', 'dropbox');
		if($this->db->tableExists('#__projects_connection_providers'))
		{
			$query = "SELECT * FROM `#__projects_connection_providers` WHERE `alias`='dropbox'";
			$this->db->setQuery($query);
			$results = $this->db->loadObjectList();
			foreach($results as $result)
			{
				$query = "DELETE FROM `#__projects_connections` WHERE `provider_id`=" . $this->db->quote($result->id);
				$this->db->setQuery($query);
				$this->db->query();
			}
			if (count($results) > 0)
			{
				$query = "DELETE FROM `#__projects_connection_providers` WHERE `alias`='dropbox'";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
