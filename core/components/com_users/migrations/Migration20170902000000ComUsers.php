<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for installing user points tables
 **/
class Migration20170902000000ComUsers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__users_points'))
		{
			$query = "CREATE TABLE `#__users_points` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `uid` int(11) NOT NULL DEFAULT '0',
			  `balance` int(11) NOT NULL DEFAULT '0',
			  `earnings` int(11) NOT NULL DEFAULT '0',
			  `credit` int(11) DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_uid` (`uid`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__users_points_config'))
		{
			$query = "CREATE TABLE `#__users_points_config` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `points` int(11) DEFAULT '0',
			  `description` varchar(255) DEFAULT NULL,
			  `alias` varchar(50) DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__users_points_services'))
		{
			$query = "CREATE TABLE `#__users_points_services` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `title` varchar(250) NOT NULL DEFAULT '',
			  `category` varchar(50) NOT NULL DEFAULT '',
			  `alias` varchar(50) NOT NULL DEFAULT '',
			  `description` varchar(255) NOT NULL DEFAULT '',
			  `unitprice` float(6,2) DEFAULT '0.00',
			  `pointsprice` int(11) DEFAULT '0',
			  `currency` varchar(50) DEFAULT 'points',
			  `maxunits` int(11) DEFAULT '0',
			  `minunits` int(11) DEFAULT '0',
			  `unitsize` int(11) DEFAULT '0',
			  `status` int(11) DEFAULT '0',
			  `restricted` int(11) DEFAULT '0',
			  `ordering` int(11) DEFAULT '0',
			  `params` text,
			  `unitmeasure` varchar(200) NOT NULL DEFAULT '',
			  `changed` datetime DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `uidx_alias` (`alias`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__users_points_subscriptions'))
		{
			$query = "CREATE TABLE `#__users_points_subscriptions` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `uid` int(11) NOT NULL DEFAULT '0',
			  `serviceid` int(11) NOT NULL DEFAULT '0',
			  `units` int(11) NOT NULL DEFAULT '1',
			  `status` int(11) NOT NULL DEFAULT '0',
			  `pendingunits` int(11) DEFAULT '0',
			  `pendingpayment` float(6,2) DEFAULT '0.00',
			  `totalpaid` float(6,2) DEFAULT '0.00',
			  `installment` int(11) DEFAULT '0',
			  `contact` varchar(20) DEFAULT '',
			  `code` varchar(10) DEFAULT '',
			  `usepoints` tinyint(2) DEFAULT '0',
			  `notes` text,
			  `added` datetime NOT NULL,
			  `updated` datetime DEFAULT NULL,
			  `expires` datetime DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__users_transactions'))
		{
			$query = "CREATE TABLE `#__users_transactions` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `uid` int(11) NOT NULL DEFAULT '0',
			  `type` varchar(20) DEFAULT NULL,
			  `description` varchar(250) DEFAULT NULL,
			  `created` datetime DEFAULT NULL,
			  `category` varchar(50) DEFAULT NULL,
			  `referenceid` int(11) DEFAULT '0',
			  `amount` int(11) DEFAULT '0',
			  `balance` int(11) DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_referenceid_category_type` (`referenceid`,`category`,`type`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__market_history'))
		{
			$query = "CREATE TABLE `#__market_history` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `itemid` int(11) NOT NULL DEFAULT '0',
			  `category` varchar(50) DEFAULT NULL,
			  `date` datetime DEFAULT NULL,
			  `action` varchar(50) DEFAULT NULL,
			  `log` text,
			  `market_value` int(11) DEFAULT '0',
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
		if ($this->db->tableExists('#__users_points'))
		{
			$query = "DROP TABLE IF EXISTS `#__users_points`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__users_points_config'))
		{
			$query = "DROP TABLE IF EXISTS `#__users_points_config`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__users_points_services'))
		{
			$query = "DROP TABLE IF EXISTS `#__users_points_services`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__users_points_subscriptions'))
		{
			$query = "DROP TABLE IF EXISTS `#__users_points_subscriptions`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__users_transactions'))
		{
			$query = "DROP TABLE IF EXISTS `#__users_transactions`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__market_history'))
		{
			$query = "DROP TABLE IF EXISTS `#__market_history`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
