<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for installing tables for DrWho component
 **/
class Migration20170703130200ComPartners extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__partner_partners'))
		{

			$query = "CREATE TABLE IF NOT EXISTS `#__partner_partners` (
			     `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	 	 		 `name` varchar(255) NOT NULL DEFAULT '', 
	  			 `date_joined` date NOT NULL DEFAULT '0000-00-00',
 				 `partner_type` int(11) NOT NULL DEFAULT 0,
 				 `site_url` varchar(255) NOT NULL DEFAULT '',
 				 `twitter_handle` varchar(255) NOT NULL DEFAULT ' ',
 				 `groups_cn` varchar(255) NOT NULL DEFAULT '',
 				 `logo_img` varchar(255) NOT NULL DEFAULT '',
  				 `QUBES_liason` varchar(255) NOT NULL DEFAULT '',
  				 `partner_liason` varchar(255) NOT NULL DEFAULT '',
  				 `activities` mediumtext NOT NULL,
 				 `state` tinyint(2) NOT NULL DEFAULT '0',  
  				 `about` mediumtext NOT NULL,
			  PRIMARY KEY (`id`),
			  KEY `idx_name` (`name`),
			  KEY `idx_state` (`state`),
			  KEY `idx_partner_type` (`partner_type`),
			  KEY `idx_QUBES_liason` (`QUBES_liason`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__partner_partner_types'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__partner_partner_types` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `internal` varchar(255) NOT NULL DEFAULT '',
			  `external` varchar(255) NOT NULL DEFAULT '',
			  `description` mediumtext NOT NULL,
			  PRIMARY KEY (`id`),
			  KEY `idx_internal` (`internal`),
			  KEY `idx_external` (`external`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}
		$this->addComponentEntry('partners');

	}

	/**
	 * Down
	 **/
	public function down()
	{
		

		if ($this->db->tableExists('#__partner_partners'))
		{
			$query = "DROP TABLE #__partner_partners";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__partner_partner_types'))
		{
			$query = "DROP TABLE #__partner_partner_types";
			$this->db->setQuery($query);
			$this->db->query();
		}

		$this->deleteComponentEntry('partners');
	}
}
