<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for ...
 **/
class Migration20130812182339ComCart extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		/*Table structure for table `#__cart` */
		$query = "CREATE TABLE IF NOT EXISTS `#__cart` (
		  `id` int(10) NOT NULL AUTO_INCREMENT,
		  `uid` int(11) NOT NULL DEFAULT '0',
		  `itemid` int(11) NOT NULL DEFAULT '0',
		  `type` varchar(20) DEFAULT NULL,
		  `quantity` int(11) NOT NULL DEFAULT '0',
		  `added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		  `selections` text,
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		
		$db->setQuery($query);
		$db->query();

		/*Table structure for table `#__cart_cart_items` */
		$query = "CREATE TABLE IF NOT EXISTS `#__cart_cart_items` (
		  `crtId` int(16) NOT NULL,
		  `sId` int(16) NOT NULL,
		  `crtiQty` int(5) DEFAULT NULL,
		  `crtiOldQty` int(5) DEFAULT NULL,
		  `crtiPrice` decimal(10,2) DEFAULT NULL,
		  `crtiOldPrice` decimal(10,2) DEFAULT NULL,
		  `crtiName` varchar(255) DEFAULT NULL,
		  `crtiAvailable` tinyint(1) DEFAULT '1',
		  PRIMARY KEY (`crtId`,`sId`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		
		$db->setQuery($query);
		$db->query();
		
		/*Table structure for table `#__cart_carts` */		
		$query = "CREATE TABLE IF NOT EXISTS `#__cart_carts` (
		  `crtId` int(16) NOT NULL AUTO_INCREMENT,
		  `crtCreated` datetime DEFAULT NULL,
		  `crtLastUpdated` datetime DEFAULT NULL,
		  `uidNumber` int(16) DEFAULT NULL,
		  PRIMARY KEY (`crtId`),
		  UNIQUE KEY `uidNumber` (`uidNumber`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		
		$db->setQuery($query);
		$db->query();
		
		/*Table structure for table `#__cart_coupons` */		
		$query = "CREATE TABLE IF NOT EXISTS `#__cart_coupons` (
		  `crtId` int(16) NOT NULL,
		  `cnId` int(16) NOT NULL,
		  `crtCnAdded` datetime DEFAULT NULL,
		  `crtCnStatus` char(15) DEFAULT NULL
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		
		$db->setQuery($query);
		$db->query();
		
		/*Table structure for table `#__cart_memberships` */		
		$query = "CREATE TABLE IF NOT EXISTS `#__cart_memberships` (
		  `crtmId` int(16) NOT NULL AUTO_INCREMENT,
		  `pId` int(16) DEFAULT NULL,
		  `crtId` int(16) DEFAULT NULL,
		  `crtmExpires` datetime DEFAULT NULL,
		  PRIMARY KEY (`crtmId`),
		  UNIQUE KEY `pId` (`pId`,`crtId`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		
		$db->setQuery($query);
		$db->query();
		
		/*Table structure for table `#__cart_saved_addresses` */		
		$query = "CREATE TABLE IF NOT EXISTS `#__cart_saved_addresses` (
		  `saId` int(16) NOT NULL AUTO_INCREMENT,
		  `uidNumber` int(16) NOT NULL,
		  `saToFirst` char(100) NOT NULL,
		  `saToLast` char(100) NOT NULL,
		  `saAddress` char(255) NOT NULL,
		  `saCity` char(25) NOT NULL,
		  `saState` char(2) NOT NULL,
		  `saZip` char(10) NOT NULL,
		  PRIMARY KEY (`saId`),
		  UNIQUE KEY `uidNumber` (`uidNumber`,`saToFirst`,`saToLast`,`saAddress`,`saCity`,`saState`,`saZip`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		
		$db->setQuery($query);
		$db->query();
		
		/*Table structure for table `#__cart_transaction_info` */		
		$query = "CREATE TABLE IF NOT EXISTS `#__cart_transaction_info` (
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
		  PRIMARY KEY (`tId`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		
		$db->setQuery($query);
		$db->query();
		
		/*Table structure for table `#__cart_transaction_items` */		
		$query = "CREATE TABLE IF NOT EXISTS `#__cart_transaction_items` (
		  `tId` int(16) NOT NULL,
		  `sId` int(16) NOT NULL,
		  `tiQty` int(5) DEFAULT NULL,
		  `tiPrice` decimal(10,2) DEFAULT NULL,
		  PRIMARY KEY (`tId`,`sId`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		
		$db->setQuery($query);
		$db->query();
		
		/*Table structure for table `#__cart_transaction_steps` */		
		$query = "CREATE TABLE IF NOT EXISTS `#__cart_transaction_steps` (
		  `tsId` int(16) NOT NULL AUTO_INCREMENT,
		  `tId` int(16) NOT NULL,
		  `tsStep` char(16) NOT NULL,
		  `tsStatus` tinyint(1) DEFAULT '0',
		  PRIMARY KEY (`tsId`),
		  UNIQUE KEY `tId` (`tId`,`tsStep`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		
		$db->setQuery($query);
		$db->query();
		
		/*Table structure for table `#__cart_transactions` */		
		$query = "CREATE TABLE IF NOT EXISTS `#__cart_transactions` (
		  `tId` int(16) NOT NULL AUTO_INCREMENT,
		  `crtId` int(16) DEFAULT NULL,
		  `tCreated` datetime DEFAULT NULL,
		  `tLastUpdated` datetime DEFAULT NULL,
		  `tStatus` char(32) DEFAULT NULL,
		  PRIMARY KEY (`tId`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		
		$db->setQuery($query);
		$db->query();
				
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		$query = "DROP TABLE IF EXISTS `#__cart`";

		$db->setQuery($query);
		$db->query();
		
		$query = "DROP TABLE IF EXISTS `#__cart_cart_items`";

		$db->setQuery($query);
		$db->query();
		
		$query = "DROP TABLE IF EXISTS `#__cart_carts`";

		$db->setQuery($query);
		$db->query();
		
		$query = "DROP TABLE IF EXISTS `#__cart_coupons`";

		$db->setQuery($query);
		$db->query();
		
		$query = "DROP TABLE IF EXISTS `#__cart_memberships`";

		$db->setQuery($query);
		$db->query();
		
		$query = "DROP TABLE IF EXISTS `#__cart_saved_addresses`";

		$db->setQuery($query);
		$db->query();
		
		$query = "DROP TABLE IF EXISTS `#__cart_transaction_info`";

		$db->setQuery($query);
		$db->query();
		
		$query = "DROP TABLE IF EXISTS `#__cart_transaction_items`";

		$db->setQuery($query);
		$db->query();
		
		$query = "DROP TABLE IF EXISTS `#__cart_transaction_steps`";

		$db->setQuery($query);
		$db->query();
		
		$query = "DROP TABLE IF EXISTS `#__cart_transactions`";

		$db->setQuery($query);
		$db->query();
	}
}