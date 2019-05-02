<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for installing services tables
 **/
class Migration20170901000000ComServices extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
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
	}

	/**
	 * Down
	 **/
	public function down()
	{
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
	}
}
