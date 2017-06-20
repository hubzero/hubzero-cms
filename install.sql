
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


# Dump of table jos_partners_partners
# ------------------------------------------------------------

DROP TABLE IF EXISTS `jos_partner_partners`;
#main partner table, everything is not null
#Create the table, the naming conventions are going to be jos_modelname_controllername
CREATE TABLE `jos_partner_partners` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '', 
  `date_joined` datetime NOT NULL DEFAULT '0000-00-00',
  `partner_type` int(11) NOT NULL DEFAULT 0,
  `site_url` varchar(255) NOT NULL DEFAULT '',
  `social_media_url` varchar(255) NOT NULL DEFAULT ' ',
  `groups_cn` varchar(255) NOT NULL DEFAULT '',
  `logo_url` varchar(255) NOT NULL DEFAULT '',
  `QUBES_liason` int(11) NOT NULL DEFAULT '0',
  `partner_liason` int(11) NOT NULL DEFAULT '0',
  `activities` mediumtext NOT NULL,
  `state` tinyint(2) NOT NULL DEFAULT '0',  
  `about` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `jos_partner_partners` WRITE;
/*!40000 ALTER TABLE `jos_partner_partners` DISABLE KEYS */;

INSERT INTO `jos_partner_partners` (`id`, `name`, `date_joined`, `partner_type`,`site_url` , `social_media_url`, `groups_cn`, `logo_url`, `QUBES_liason`,
`partner_liason`, `activities`, `state`, `about`)
VALUES
	(1,'National Science Foundation','2014-09-17',2,'www.nsf.gov','https://twitter.com/NSF',
		'nsf', 'https://www.nsf.gov/news/mmg/media/images/nsf_logo_f_272f4777-f5c4-4e49-8a2e-468e89b64b61_f.jpg', 
    '1234', '1234','activites', 1,
    '<!-- {FORMAT:HTML} --><p>The National Science Foundation (NSF) is an independent federal agency created by Congress in 1950 "to promote the
		 progress of science; to advance the national health, prosperity, and welfare; to secure the national defense…"’\n</p>');
/*!40000 ALTER TABLE `jos_partner_partners` ENABLE KEYS */;
UNLOCK TABLES;


#partner-type table
# drop the table if it exists:
DROP TABLE IF EXISTS `.jos_partner_type_partner_types`;

CREATE TABLE `jos_partner_type_partner_types` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`internal` varchar(255) NOT NULL DEFAULT '',
`external` varchar(255) NOT NULL DEFAULT '',
`description` mediumtext NOT NULL,
PRIMARY KEY(`id`)
)ENGINE = InnoDB DEFAULT CHARSET=latin1;

#insert new values
INSERT INTO `jos_partner_type_partner_types`(`id`,`internal`,`external`,`description`)
VALUES
(1,'Collaborators', 'Consortium Members', '<!-- {FORMAT:HTML} --><p>Sharing information, coordinating efforts \n</p>'),
(2,'Alliance Partners', 'Partners', '<!-- {FORMAT:HTML} --><p>Joint programming (decision-making power is shared or transferred) \n</p>' ),
(3,'Venture Partners', 'Featured Partners', '<!-- {FORMAT:HTML} --><p>Joint ventures \n</p>' ),
(4,'Funding Partners', 'Sponsors', '<!-- {FORMAT:HTML} --><p>Recipient-donor relationship - determination for allocating funds \n</p>' ),
(5,'Host Partners', 'Leadership Team', '<!-- {FORMAT:HTML} --><p>Cost-sharing; grant match; sharing of benefits and costs \n</p>' );

UNLOCK TABLES;