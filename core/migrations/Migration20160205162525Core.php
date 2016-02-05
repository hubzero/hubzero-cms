<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for creating table #__audit_results
 **/
class Migration20160205162525Core extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__audit_results'))
		{
			$query = "CREATE TABLE `#__audit_results` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `scope` varchar(100) NOT NULL DEFAULT '',
				  `scope_id` int(11) unsigned NOT NULL DEFAULT '0',
				  `processed` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				  `status` tinyint(3) NOT NULL DEFAULT '0',
				  `notes` tinytext NOT NULL,
				  `test_id` varchar(255) NOT NULL DEFAULT '',
				  PRIMARY KEY (`id`),
				  KEY `idx_scope_scope_id` (`scope`,`scope_id`),
				  KEY `idx_status` (`status`),
				  KEY `idx_test_id` (`test_id`)
				) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=latin1";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__audit_results'))
		{
			$query = "DROP TABLE `#__audit_results`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}