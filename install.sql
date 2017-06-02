
#author=Jacob Harless - jrharless@email.wm.edu
#mysql database for partners, based off 
# ************************************************************
# Sequel Pro SQL dump
# Version 4096
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: 127.0.0.1 (MySQL 5.5.22)
# Database: hubzero
# Generation Time: 2014-09-18 14:13:00 +0000
# ************************************************************


# Dump of table jos_partners
# ------------------------------------------------------------

DROP TABLE IF EXISTS `jos_partners`;

CREATE TABLE `jos_partners` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `date_joined` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `partner_type` varchar(100) NOT NULL DEFAULT '',
  `site_url` varchar(255) NOT NULL DEFAULT '',
  `social_media_url` varchar(255) NOT NULL DEFAULT '',
  `QUBES_group_url` varchar(255) NOT NULL DEFAULT '',
  `logo_url` varchar(255) NOT NULL DEFAULT '',
  `state` tinyint(2) NOT NULL DEFAULT '0',
  `about` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `jos_partners` WRITE;
/*!40000 ALTER TABLE `jos_partners` DISABLE KEYS */;

INSERT INTO `jos_partners` (`id`, `name`, `date_joined`, `partner_type`,'site_url' , 'social_media_url', `QUBES_group_url`, `logo_url`, `state`, `about`)
VALUES
	(1,'National Science Foundation','2014-09-17','type1','www.nsf.gov','https://twitter.com/NSF', 
		'https://qubeshub.org/community/groups', 'https://www.nsf.gov/news/mmg/media/images/nsf_logo_f_272f4777-f5c4-4e49-8a2e-468e89b64b61_f.jpg'
		'<!-- {FORMAT:HTML} --><p>The National Science Foundation (NSF) is an independent federal agency created by Congress in 1950 "to promote the
		 progress of science; to advance the national health, prosperity, and welfare; to secure the national defense…"’\n</p>'),
	
/*!40000 ALTER TABLE `jos_partners` ENABLE KEYS */;
UNLOCK TABLES;
