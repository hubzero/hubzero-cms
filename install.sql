
#author=Jacob Harless - jrharless@email.wm.edu
#mysql database for parterns
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
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) unsigned NOT NULL DEFAULT '0',
  `doctor` tinyint(2) NOT NULL DEFAULT '0',
  `friend` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `enemy` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `bio` mediumtext NOT NULL,
  `state` tinyint(2) NOT NULL DEFAULT '0',
  `species` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `jos_partners` WRITE;
/*!40000 ALTER TABLE `jos_partners` DISABLE KEYS */;

INSERT INTO `jos_partners` (`id`, `name`, `created`, `created_by`, `doctor`, `friend`, `enemy`, `bio`, `state`, `species`)
VALUES
	(1,'First Doctor','2014-02-04 09:23:12',1001,1,0,0,'',1,'timelord'),
	(2,'Second Doctor','2014-02-04 09:24:42',1001,1,0,0,'',1,'timelord'),
	(3,'Third Doctor','2014-09-17 13:48:42',1001,1,0,0,'',1,'timelord'),
	(4,'Fourth Doctor','2014-09-17 13:48:42',1001,1,0,0,'',1,'timelord'),
	(5,'Fifth Doctor','2014-09-17 13:48:42',1001,1,0,0,'',1,'timelord'),
	(6,'Sixth Doctor','2014-09-17 13:48:42',1001,1,0,0,'',1,'timelord'),
	(7,'Seventh Doctor','2014-09-17 13:48:42',1001,1,0,0,'<!-- {FORMAT:HTML} -->',1,'timelord'),
	(8,'Eighth Doctor','2014-09-17 13:48:42',1001,1,0,0,'',1,'timelord'),
	(9,'Ninth Doctor','2014-09-17 13:48:42',1001,1,0,0,'<!-- {FORMAT:HTML} -->',1,'timelord'),
	(10,'Tenth Doctor','2014-09-17 13:48:42',0,1,0,0,'<!-- {FORMAT:HTML} --><p>The Tenth Doctor was a charismatic mixture of apparent opposites… He could show extraordinary kindness and sensitivity, but he himself admitted he was a man who gave no second chances. As Donna Noble pointed out to him, ‘I think sometimes you need somebody to stop you.’\n</p>',1,'timelord'),
	(11,'Eleventh Doctor','2014-09-17 13:48:42',1001,1,0,0,'<!-- {FORMAT:HTML} -->',1,'timelord'),
	(12,'Twelfth Doctor','2014-09-17 13:48:42',1001,1,0,0,'',1,'timelord'),
	(13,'Dalek Emperor','2014-09-17 13:48:42',1001,0,0,1,'EXTERMINATE!',1,'dalek'),
	(14,'Rose Tyler','2014-09-17 13:48:42',1001,0,1,0,'Rose Tyler was born to Jackie and Pete Tyler around 1986. Her father died when hit by a car while she was still a baby.\n\nShe attended Jericho Street Junior School, where she joined the gymnastics club, and won a bronze medal in competition. (TV: Rose) She left school aged sixteen to pursue a romantic relationship with local Jimmy Stone, which ended badly and apparently led to her lack of A-Levels.',1,'human'),
	(15,'Strax','2014-09-17 13:48:42',1001,0,1,0,'Considering he?s a member of a clone race, Commander Strax is a remarkable individual. We?ve seen the Doctor encounter many Sontarans and whilst every other member of this belligerent species has been guided by a single-minded passion for war, Strax is a nurse. And a good one. We?ve witnessed him save the life of a child in a warzone, give medical advice to a weary soldier and Strax even had himself gene-spliced so he could be fit for all nursing duties. When baby Melody appeared to need a feed he proudly declared, ?I can produce magnificent quantities of lactic fluid!?',1,'sontaran'),
	(16,'Sarah Jane Smith','2014-09-17 13:48:42',1001,0,1,0,'<!-- {FORMAT:HTML} --><p>Sarah Jane and the Doctor didn?t get off to the best of starts? She was operating as an undercover journalist when they met, impersonating Lavinia Smith in order to infiltrate a group of scientists, hoping to get a good story and find out why so many of them were vanishing. The Doctor saw straight through her pretence but showed very little interest in the young reporter, dashing off to the middle ages in order to trace the source of the disappearances. Little did he know that Sarah had sneaked aboard the TARDIS? He unwittingly whisked her back several centuries, landing her slap bang in the middle of an adventure with a Sontaran and primitive feuding forces. But Sarah wasn?t fazed for a moment, quickly galvanising the locals into an attack on the villainous Irongron and even preaching&nbsp;?women?s lib? to the startled cooks in his castle!\n</p>',1,'human'),
	(17,'TARDIS','2014-09-17 13:48:42',0,0,1,0,'<!-- {FORMAT:WIKI} -->The TARDIS can travel to any point in all of time and space and is bigger on the inside than the outside due to trans-dimensional engineering ? a key Time Lord discovery. It has a library, a swimming pool, a large boot room, an enormous chamber jammed with clothes and many more nooks, crannies and secrets just waiting to be discovered?',1,'???');

/*!40000 ALTER TABLE `jos_partners` ENABLE KEYS */;
UNLOCK TABLES;

