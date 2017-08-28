<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing Publications - Forks plugin
 **/
class Migration20170623162652PlgPublicationsForks extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('publications', 'forks', 0);

		if ($this->db->tableExists('#__publication_versions'))
		{
			if (!$this->db->tableHasField('#__publication_versions', 'forked_from'))
			{
				$query = "ALTER TABLE `#__publication_versions` ADD `forked_from` int(11) NOT NULL DEFAULT '0'";
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
		$this->deletePluginEntry('publications', 'forks');

		if ($this->db->tableExists('#__publication_versions'))
		{
			if ($this->db->tableHasField('#__publication_versions', 'forked_from'))
			{
				$query = "ALTER TABLE `#__publication_versions` DROP `forked_from`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
