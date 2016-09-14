<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script to add missing fields to #__resource_stats_cluster table
 **/
class Migration2016090510300000Core extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__resource_stats_clusters') && !$this->db->tableHasField('#__resource_stats_clusters', 'clustersize'))
		{
			$query = "ALTER TABLE `#__resource_stats_clusters` ADD COLUMN `clustersize` varchar(255) NOT NULL DEFAULT '' AFTER `resid`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__resource_stats_clusters') && !$this->db->tableHasField('#__resource_stats_clusters', 'cluster_start'))
		{
			$query = "ALTER TABLE `#__resource_stats_clusters` ADD COLUMN `cluster_start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `clustersize`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__resource_stats_clusters') && !$this->db->tableHasField('#__resource_stats_clusters', 'cluster_end'))
		{
			$query = "ALTER TABLE `#__resource_stats_clusters` ADD COLUMN `cluster_end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `cluster_start`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__resource_stats_clusters') && !$this->db->tableHasField('#__resource_stats_clusters', 'institution'))
		{
			$query = "ALTER TABLE `#__resource_stats_clusters` ADD COLUMN  `institution` varchar(255) NOT NULL DEFAULT '' AFTER `cluster_end`";
			$this->db->setQuery($query);
			$this->db->query();
		}

	}

	/**
	 * Down
	 **/
	public function down()
	{
		/* This is a repair migration. A down method would be invalid */
		/* as this change should have happened in Migration20120101000001Core.php */
		/* Repair is only needed on some hubs, perhaps predating that migration.  */
	}
}
