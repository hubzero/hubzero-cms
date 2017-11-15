<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for installing antispam blacklisted words table
 **/
class Migration20170901000000PlgAntispamBlacklist extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__antispam_words'))
		{
			$query = "CREATE TABLE `#__antispam_words` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `word` varchar(256) DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__antispam_words'))
		{
			$query = "DROP TABLE IF EXISTS `#__antispam_words`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
