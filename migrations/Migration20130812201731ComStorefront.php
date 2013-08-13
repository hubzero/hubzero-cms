<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for ...
 **/
class Migration20130812201731ComStorefront extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		/*Table structure for table `#__storefront_collections` */
		$query = "CREATE TABLE IF NOT EXISTS `#__storefront_collections` (
		  `cId` char(50) NOT NULL,
		  `cName` varchar(64) DEFAULT NULL,
		  `cParent` int(16) DEFAULT NULL,
		  `cActive` tinyint(1) DEFAULT NULL,
		  `cType` char(10) DEFAULT NULL,
		  PRIMARY KEY (`cId`),
		  KEY `cActive` (`cActive`),
		  KEY `cParent` (`cParent`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		$db->setQuery($query);
		$db->query();
		
		/*Table structure for table `#__storefront_coupon_actions` */
		$query = "CREATE TABLE IF NOT EXISTS `#__storefront_coupon_actions` (
		  `cnId` int(16) NOT NULL,
		  `cnaAction` char(25) DEFAULT NULL,
		  `cnaVal` char(255) DEFAULT NULL,
		  UNIQUE KEY `cnId` (`cnId`,`cnaAction`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		$db->setQuery($query);
		$db->query();
		
		/*Table structure for table `#__storefront_coupon_conditions` */
		$query = "CREATE TABLE IF NOT EXISTS `#__storefront_coupon_conditions` (
		  `cnId` int(16) NOT NULL,
		  `cncRule` char(100) DEFAULT NULL,
		  `cncVal` char(255) DEFAULT NULL
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		$db->setQuery($query);
		$db->query();
		
		/*Table structure for table `#__storefront_coupon_objects` */
		$query = "CREATE TABLE IF NOT EXISTS `#__storefront_coupon_objects` (
		  `cnId` int(16) NOT NULL,
		  `cnoObjectId` int(16) DEFAULT NULL,
		  `cnoObjectsLimit` int(5) DEFAULT '0' COMMENT 'How many objects can be applied to. 0 - unlimited',
		  UNIQUE KEY `cnId` (`cnId`,`cnoObjectId`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		$db->setQuery($query);
		$db->query();
		
		/*Table structure for table `#__storefront_coupons` */
		$query = "CREATE TABLE IF NOT EXISTS `#__storefront_coupons` (
		  `cnId` int(16) NOT NULL AUTO_INCREMENT,
		  `cnCode` char(25) DEFAULT NULL,
		  `cnDescription` char(255) DEFAULT NULL,
		  `cnExpires` date DEFAULT NULL,
		  `cnUseLimit` int(5) unsigned DEFAULT NULL,
		  `cnObject` char(15) NOT NULL,
		  `cnActive` tinyint(1) DEFAULT '1',
		  PRIMARY KEY (`cnId`),
		  UNIQUE KEY `Unique code` (`cnCode`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		$db->setQuery($query);
		$db->query();
		
		/*Table structure for table `#__storefront_option_groups` */
		$query = "CREATE TABLE IF NOT EXISTS `#__storefront_option_groups` (
		  `ogId` int(16) NOT NULL AUTO_INCREMENT,
		  `ogName` char(16) DEFAULT NULL,
		  PRIMARY KEY (`ogId`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		$db->setQuery($query);
		$db->query();
		
		/*Table structure for table `#__storefront_options` */
		$query = "CREATE TABLE IF NOT EXISTS `#__storefront_options` (
		  `oId` int(16) NOT NULL AUTO_INCREMENT,
		  `ogId` int(16) DEFAULT NULL COMMENT 'Foreign key to option-groups',
		  `oName` char(255) DEFAULT NULL,
		  PRIMARY KEY (`oId`),
		  UNIQUE KEY `ogId` (`ogId`,`oName`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		$db->setQuery($query);
		$db->query();
		
		/*Table structure for table `#__storefront_product_collections` */
		$query = "CREATE TABLE IF NOT EXISTS `#__storefront_product_collections` (
		  `cllId` int(16) NOT NULL AUTO_INCREMENT,
		  `pId` int(16) NOT NULL,
		  `cId` char(50) NOT NULL,
		  PRIMARY KEY (`cllId`,`pId`,`cId`),
		  UNIQUE KEY `pId` (`pId`,`cId`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		$db->setQuery($query);
		$db->query();
		
		/*Table structure for table `#__storefront_product_option_groups` */
		$query = "CREATE TABLE IF NOT EXISTS `#__storefront_product_option_groups` (
		  `pId` int(16) NOT NULL,
		  `ogId` int(16) NOT NULL,
		  PRIMARY KEY (`pId`,`ogId`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		$db->setQuery($query);
		$db->query();
		
		/*Table structure for table `#__storefront_product_types` */
		$query = "CREATE TABLE IF NOT EXISTS `#__storefront_product_types` (
		  `ptId` int(16) NOT NULL AUTO_INCREMENT,
		  `ptName` char(128) DEFAULT NULL,
		  `ptModel` char(25) DEFAULT 'normal',
		  PRIMARY KEY (`ptId`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		$db->setQuery($query);
		$db->query();
		
		/*Table structure for table `#__storefront_products` */
		$query = "CREATE TABLE IF NOT EXISTS `#__storefront_products` (
		  `pId` int(16) NOT NULL AUTO_INCREMENT,
		  `ptId` int(16) NOT NULL COMMENT 'Product type ID. Foreign key to product_types table',
		  `pName` char(128) DEFAULT NULL,
		  `pTagline` tinytext,
		  `pDescription` text,
		  `pFeatures` text,
		  `pActive` tinyint(1) DEFAULT '1',
		  PRIMARY KEY (`pId`),
		  KEY `pActive` (`pActive`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		$db->setQuery($query);
		$db->query();
		
		/*Table structure for table `#__storefront_sku_meta` */
		$query = "CREATE TABLE IF NOT EXISTS `#__storefront_sku_meta` (
		  `smId` int(16) NOT NULL AUTO_INCREMENT,
		  `sId` int(16) NOT NULL,
		  `smKey` varchar(100) DEFAULT NULL,
		  `smValue` varchar(100) DEFAULT NULL,
		  PRIMARY KEY (`smId`),
		  UNIQUE KEY `sId` (`sId`,`smKey`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		$db->setQuery($query);
		$db->query();
		
		/*Table structure for table `#__storefront_sku_options` */
		$query = "CREATE TABLE IF NOT EXISTS `#__storefront_sku_options` (
		  `sId` int(16) NOT NULL,
		  `oId` int(16) NOT NULL,
		  PRIMARY KEY (`sId`,`oId`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		$db->setQuery($query);
		$db->query();
		
		/*Table structure for table `#__storefront_skus` */
		$query = "CREATE TABLE IF NOT EXISTS `#__storefront_skus` (
		  `sId` int(16) NOT NULL AUTO_INCREMENT,
		  `pId` int(16) DEFAULT NULL COMMENT 'Foreign key to products',
		  `sSku` char(16) DEFAULT NULL,
		  `sWeight` decimal(10,2) DEFAULT NULL,
		  `sPrice` decimal(10,2) DEFAULT NULL,
		  `sDescriprtion` text,
		  `sFeatures` text,
		  `sTrackInventory` tinyint(1) DEFAULT '0',
		  `sInventory` int(11) DEFAULT '0',
		  `sEnumerable` tinyint(1) DEFAULT '1',
		  `sAllowMultiple` tinyint(1) DEFAULT '1',
		  `sActive` tinyint(1) DEFAULT '1',
		  PRIMARY KEY (`sId`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";

	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		$query = "DROP TABLE IF EXISTS `#__storefront_collections`";

		$db->setQuery($query);
		$db->query();
		
		$query = "DROP TABLE IF EXISTS `#__storefront_coupon_actions`";

		$db->setQuery($query);
		$db->query();
		
		$query = "DROP TABLE IF EXISTS `#__storefront_coupon_conditions`";

		$db->setQuery($query);
		$db->query();
		
		$query = "DROP TABLE IF EXISTS `#__storefront_coupon_objects`";

		$db->setQuery($query);
		$db->query();
		
		$query = "DROP TABLE IF EXISTS `#__storefront_coupons`";

		$db->setQuery($query);
		$db->query();
		
		$query = "DROP TABLE IF EXISTS `#__storefront_option_groups`";

		$db->setQuery($query);
		$db->query();
		
		$query = "DROP TABLE IF EXISTS `#__storefront_options`";

		$db->setQuery($query);
		$db->query();
		
		$query = "DROP TABLE IF EXISTS `#__storefront_product_collections`";

		$db->setQuery($query);
		$db->query();
		
		$query = "DROP TABLE IF EXISTS `#__storefront_product_option_groups`";

		$db->setQuery($query);
		$db->query();
		
		$query = "DROP TABLE IF EXISTS `#__storefront_product_types`";

		$db->setQuery($query);
		$db->query();
		
		$query = "DROP TABLE IF EXISTS `#__storefront_products`";

		$db->setQuery($query);
		$db->query();

		$query = "DROP TABLE IF EXISTS `#__storefront_sku_meta`";

		$db->setQuery($query);
		$db->query();
		
		$query = "DROP TABLE IF EXISTS `#__storefront_sku_options`";

		$db->setQuery($query);
		$db->query();
		
		$query = "DROP TABLE IF EXISTS `#__storefront_skus`";

		$db->setQuery($query);
		$db->query();
	}
}