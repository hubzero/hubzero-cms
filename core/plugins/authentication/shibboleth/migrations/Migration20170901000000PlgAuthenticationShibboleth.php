<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for installing shibboleth tables
 **/
class Migration20170901000000PlgAuthenticationShibboleth extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__shibboleth_sessions'))
		{
			$query = "CREATE TABLE `#__shibboleth_sessions` (
			  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			  `session_key` varchar(200) NOT NULL,
			  `data` text NOT NULL,
			  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `id` (`id`),
			  UNIQUE KEY `session_key` (`session_key`)
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
		if ($this->db->tableExists('#__shibboleth_sessions'))
		{
			$query = "DROP TABLE IF EXISTS `#__shibboleth_sessions`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
