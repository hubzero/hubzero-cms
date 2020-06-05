<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for installing cart tables
 **/
class Migration20170901000000ComCart extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__cart_carts'))
		{
			$query = "CREATE TABLE `#__cart_carts` (
			  `crtId` int(16) NOT NULL AUTO_INCREMENT,
			  `crtCreated` datetime DEFAULT NULL,
			  `crtLastUpdated` datetime DEFAULT NULL,
			  `uidNumber` int(16) DEFAULT NULL,
			  PRIMARY KEY (`crtId`),
			  UNIQUE KEY `uidx_uidNumber` (`uidNumber`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__cart_cart_items'))
		{
			$query = "CREATE TABLE `#__cart_cart_items` (
			  `crtId` int(16) NOT NULL,
			  `sId` int(16) NOT NULL,
			  `crtiQty` int(5) DEFAULT NULL,
			  `crtiOldQty` int(5) DEFAULT NULL,
			  `crtiPrice` decimal(10,2) DEFAULT NULL,
			  `crtiOldPrice` decimal(10,2) DEFAULT NULL,
			  `crtiName` varchar(255) DEFAULT NULL,
			  `crtiAvailable` tinyint(1) DEFAULT '1',
			  PRIMARY KEY (`crtId`,`sId`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__cart_coupons'))
		{
			$query = "CREATE TABLE `#__cart_coupons` (
			  `crtId` int(16) NOT NULL,
			  `cnId` int(16) NOT NULL,
			  `crtCnAdded` datetime DEFAULT NULL,
			  `crtCnStatus` char(15) DEFAULT NULL
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__cart_downloads'))
		{
			$query = "CREATE TABLE `#__cart_downloads` (
			  `dId` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `uId` int(11) DEFAULT NULL,
			  `sId` int(11) DEFAULT NULL,
			  `dDownloaded` datetime DEFAULT NULL,
			  `dStatus` tinyint(1) DEFAULT '1',
			  `dIp` int(10) unsigned DEFAULT NULL,
			  PRIMARY KEY (`dId`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__cart_memberships'))
		{
			$query = "CREATE TABLE `#__cart_memberships` (
			  `crtmId` int(16) NOT NULL AUTO_INCREMENT,
			  `pId` int(16) DEFAULT NULL,
			  `crtId` int(16) DEFAULT NULL,
			  `crtmExpires` datetime DEFAULT NULL,
			  PRIMARY KEY (`crtmId`),
			  UNIQUE KEY `uidx_pId_crtId` (`pId`,`crtId`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__cart_meta'))
		{
			$query = "CREATE TABLE `#__cart_meta` (
			  `mtId` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `scope_id` int(11) NOT NULL DEFAULT '0',
			  `scope` varchar(100) NOT NULL DEFAULT '',
			  `mtKey` varchar(100) NOT NULL DEFAULT '',
			  `mtValue` text,
			  PRIMARY KEY (`mtId`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__cart_saved_addresses'))
		{
			$query = "CREATE TABLE `#__cart_saved_addresses` (
			  `saId` int(16) NOT NULL AUTO_INCREMENT,
			  `uidNumber` int(16) NOT NULL,
			  `saToFirst` char(100) NOT NULL,
			  `saToLast` char(100) NOT NULL,
			  `saAddress` char(255) NOT NULL,
			  `saCity` char(25) NOT NULL,
			  `saState` char(2) NOT NULL,
			  `saZip` char(10) NOT NULL,
			  PRIMARY KEY (`saId`),
			  UNIQUE KEY `uidx_uidNumber_saToFirst_saToLast_saAddress_saZip` (`uidNumber`,`saToFirst`,`saToLast`,`saAddress`(100),`saZip`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__cart_transactions'))
		{
			$query = "CREATE TABLE `#__cart_transactions` (
			  `tId` int(16) NOT NULL AUTO_INCREMENT,
			  `crtId` int(16) DEFAULT NULL,
			  `tCreated` datetime DEFAULT NULL,
			  `tLastUpdated` datetime DEFAULT NULL,
			  `tStatus` char(32) DEFAULT NULL,
			  PRIMARY KEY (`tId`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__cart_transaction_info'))
		{
			$query = "CREATE TABLE `#__cart_transaction_info` (
			  `tId` int(16) NOT NULL,
			  `tiShippingToFirst` char(100) DEFAULT NULL,
			  `tiShippingToLast` char(100) DEFAULT NULL,
			  `tiShippingAddress` char(255) DEFAULT NULL,
			  `tiShippingCity` char(25) DEFAULT NULL,
			  `tiShippingState` char(2) DEFAULT NULL,
			  `tiShippingZip` char(10) DEFAULT NULL,
			  `tiTotal` decimal(10,2) DEFAULT NULL,
			  `tiSubtotal` decimal(10,2) DEFAULT NULL,
			  `tiTax` decimal(10,2) DEFAULT NULL,
			  `tiShipping` decimal(10,2) DEFAULT NULL,
			  `tiShippingDiscount` decimal(10,2) DEFAULT NULL,
			  `tiDiscounts` decimal(10,2) DEFAULT NULL,
			  `tiItems` text,
			  `tiPerks` text,
			  `tiMeta` text,
			  `tiCustomerStatus` char(15) DEFAULT 'unconfirmed',
			  `tiNotes` text,
			  PRIMARY KEY (`tId`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__cart_transaction_items'))
		{
			$query = "CREATE TABLE `#__cart_transaction_items` (
			  `tId` int(16) NOT NULL,
			  `sId` int(16) NOT NULL,
			  `tiQty` int(5) DEFAULT NULL,
			  `tiPrice` decimal(10,2) DEFAULT NULL,
			  `tiMeta` text,
			  PRIMARY KEY (`tId`,`sId`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__cart_transaction_steps'))
		{
			$query = "CREATE TABLE `#__cart_transaction_steps` (
			  `tsId` int(16) NOT NULL AUTO_INCREMENT,
			  `tId` int(16) NOT NULL,
			  `tsStep` char(16) NOT NULL,
			  `tsStatus` tinyint(1) DEFAULT '0',
			  `tsMeta` char(255) DEFAULT NULL,
			  PRIMARY KEY (`tsId`)
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
		if ($this->db->tableExists('#__cart_carts'))
		{
			$query = "DROP TABLE IF EXISTS `#__cart_carts`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__cart_cart_items'))
		{
			$query = "DROP TABLE IF EXISTS `#__cart_cart_items`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__cart_coupons'))
		{
			$query = "DROP TABLE IF EXISTS `#__cart_coupons`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__cart_downloads'))
		{
			$query = "DROP TABLE IF EXISTS `#__cart_downloads`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__cart_memberships'))
		{
			$query = "DROP TABLE IF EXISTS `#__cart_memberships`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__cart_meta'))
		{
			$query = "DROP TABLE IF EXISTS `#__cart_meta`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__cart_saved_addresses'))
		{
			$query = "DROP TABLE IF EXISTS `#__cart_saved_addresses`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__cart_transactions'))
		{
			$query = "DROP TABLE IF EXISTS `#__cart_transactions`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__cart_transaction_info'))
		{
			$query = "DROP TABLE IF EXISTS `#__cart_transaction_info`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__cart_transaction_items'))
		{
			$query = "DROP TABLE IF EXISTS `#__cart_transaction_items`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__cart_transaction_steps'))
		{
			$query = "DROP TABLE IF EXISTS `#__cart_transaction_steps`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
