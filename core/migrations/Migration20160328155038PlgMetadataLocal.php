<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding local metadata support
 **/
class Migration20160328155038PlgMetadataLocal extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('metadata','local', 0);

		if (!$this->db->tableExists('#__file_metadata'))
		{
			$query = "CREATE TABLE `#__file_metadata` (
					  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
					  `path` varchar(255) NOT NULL DEFAULT '',
					  `key` varchar(255) NOT NULL DEFAULT '',
					  `value` varchar(255) DEFAULT NULL,
					  PRIMARY KEY (`id`)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('metadata','local');

		if (!$this->db->tableExists('#__file_metadata'))
		{
			$query = "CREATE TABLE `#__file_metadata` (
					  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
					  `path` varchar(255) NOT NULL DEFAULT '',
					  `key` varchar(255) NOT NULL DEFAULT '',
					  `value` varchar(255) DEFAULT NULL,
					  PRIMARY KEY (`id`)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
