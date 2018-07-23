<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding com_fmns tables
 **/
class Migration20180712000001ComFmns extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
    if (!$this->db->tableExists('#__fmn_fmns'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__fmn_fmns` (
			   `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	 	 		 `name` varchar(255) NOT NULL DEFAULT '',
	  		 `about` text NOT NULL DEFAULT '',
 				 `group_cn` varchar(255) DEFAULT NULL,
 				 `start_date` date NOT NULL DEFAULT '0000-00-00',
 				 `stop_date` date NOT NULL DEFAULT '0000-00-00',
 				 `reg_due_date` date NULL DEFAULT '0000-00-00',
 				 `reg_status` tinyint(1) NOT NULL DEFAULT 0,
  			 `reg_event_id` int(11) DEFAULT NULL,
  			 `fmn_event_id` int(11) DEFAULT NULL,
  			 `featured` tinyint(1) NOT NULL DEFAULT 0,
  			 `state` tinyint(1) NOT NULL DEFAULT 0,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `group_cn` (`group_cn`),
        KEY `idx_state` (`state`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

    if (!$this->db->tableExists('#__fmn_sponsors'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__fmn_sponsors` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `fmn_id` int(11) unsigned NOT NULL DEFAULT 0,
			  `partner_id` int(11) unsigned NOT NULL DEFAULT 0,
			  PRIMARY KEY (`id`),
			  KEY `idx_fmn_id` (`fmn_id`),
			  KEY `idx_partner_id` (`partner_id`)
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
    if ($this->db->tableExists('#__fmn_fmns'))
		{
			$query = "DROP TABLE #__fmn_fmns";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__fmn_sponsors'))
		{
			$query = "DROP TABLE #__fmn_sponsors";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
