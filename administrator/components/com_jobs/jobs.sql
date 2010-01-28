{\rtf1\ansi\ansicpg1252\cocoartf1038\cocoasubrtf250
{\fonttbl\f0\fswiss\fcharset0 Helvetica;}
{\colortbl;\red255\green255\blue255;}
\margl1440\margr1440\vieww15240\viewh10240\viewkind0
\pard\tx720\tx1440\tx2160\tx2880\tx3600\tx4320\tx5040\tx5760\tx6480\tx7200\tx7920\tx8640\ql\qnatural\pardirnatural

\f0\fs24 \cf0 CREATE TABLE `jos_users_points_subscriptions` (\
  `id` int(11) NOT NULL auto_increment,\
  `uid` int(11) NOT NULL default '0',\
  `serviceid` int(11) NOT NULL default '0',\
  `units` int(11) NOT NULL default '1',\
  `status` int(11) NOT NULL default '0',\
  `pendingunits` int(11) default '0',\
  `pendingpayment` float(6,2) default '0.00',\
  `totalpaid` float(6,2) default '0.00',\
  `installment` int(11) default '0',\
  `contact` varchar(20) default '',\
  `code` varchar(10) default '',\
  `usepoints` tinyint(2) default '0',\
  `notes` text,\
  `added` datetime NOT NULL,\
  `updated` datetime default NULL,\
  `expires` datetime default NULL,\
  PRIMARY KEY  (`id`)\
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;\
\
CREATE TABLE `jos_users_points_services` (\
  `id` int(11) NOT NULL auto_increment,\
  `title` varchar(250) NOT NULL default '',\
  `category` varchar(50) NOT NULL default '',\
  `alias` varchar(50) NOT NULL default '',\
  `description` varchar(255) NOT NULL default '',\
  `unitprice` float(6,2) default '0.00',\
  `pointsprice` int(11) default '0',\
  `currency` varchar(50) default 'points',\
  `maxunits` int(11) default '0',\
  `minunits` int(11) default '0',\
  `unitsize` int(11) default '0',\
  `status` int(11) default '0',\
  `params` text,\
  `unitmeasure` varchar(200) NOT NULL default '',\
  `changed` datetime default NULL,\
  PRIMARY KEY  (`id`),\
  UNIQUE KEY `alias` (`alias`)\
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;\
\
CREATE TABLE `jos_jobs_types` (\
  `id` int(11) NOT NULL auto_increment,\
  `category` varchar(150) NOT NULL default '',\
  PRIMARY KEY  (`id`)\
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;\
\
INSERT INTO `jos_jobs_types` (`id`,`category`) VALUES ('1','Full-time');\
INSERT INTO `jos_jobs_types` (`id`,`category`) VALUES ('2','Part-time');\
INSERT INTO `jos_jobs_types` (`id`,`category`) VALUES ('3','Contract');\
INSERT INTO `jos_jobs_types` (`id`,`category`) VALUES ('4','Internship');\
INSERT INTO `jos_jobs_types` (`id`,`category`) VALUES ('5','Temporary');\
\
CREATE TABLE `jos_jobs_stats` (\
  `id` int(11) NOT NULL auto_increment,\
  `itemid` int(11) NOT NULL,\
  `category` varchar(11) NOT NULL default '',\
  `total_viewed` int(11) default '0',\
  `total_shared` int(11) default '0',\
  `viewed_today` int(11) default '0',\
  `lastviewed` datetime default NULL,\
  PRIMARY KEY  (`id`)\
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;\
\
CREATE TABLE `jos_jobs_shortlist` (\
  `id` int(11) NOT NULL auto_increment,\
  `emp` int(11) NOT NULL default '0',\
  `seeker` int(11) NOT NULL default '0',\
  `category` varchar(11) NOT NULL default 'resume',\
  `jobid` int(11) default '0',\
  `added` datetime default NULL,\
  PRIMARY KEY  (`id`)\
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;\
\
CREATE TABLE `jos_jobs_seekers` (\
  `id` int(11) NOT NULL auto_increment,\
  `uid` int(11) NOT NULL default '0',\
  `active` int(11) NOT NULL default '0',\
  `lookingfor` varchar(255) default '',\
  `tagline` varchar(255) default '',\
  `sought_cid` int(11) default '0',\
  `sought_type` int(11) default '0',\
  PRIMARY KEY  (`id`)\
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;\
\
CREATE TABLE `jos_jobs_resumes` (\
  `id` int(11) NOT NULL auto_increment,\
  `uid` int(11) NOT NULL default '0',\
  `created` datetime NOT NULL default '0000-00-00 00:00:00',\
  `title` varchar(100) default NULL,\
  `filename` varchar(100) default NULL,\
  `main` tinyint(2) default '1',\
  PRIMARY KEY  (`id`)\
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;\
\
CREATE TABLE `jos_jobs_prefs` (\
  `id` int(11) NOT NULL auto_increment,\
  `uid` int(10) NOT NULL default '0',\
  `category` varchar(20) NOT NULL default 'resume',\
  `filters` text,\
  PRIMARY KEY  (`id`)\
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;\
\
CREATE TABLE `jos_jobs_openings` (\
  `id` int(11) NOT NULL auto_increment,\
  `cid` int(11) default '0',\
  `employerid` int(11) NOT NULL default '0',\
  `code` int(11) NOT NULL default '0',\
  `title` varchar(200) NOT NULL default '',\
  `companyName` varchar(200) NOT NULL default '',\
  `companyLocation` varchar(200) default '',\
  `companyLocationCountry` varchar(100) default '',\
  `companyWebsite` varchar(200) default '',\
  `description` text,\
  `addedBy` int(11) NOT NULL default '0',\
  `editedBy` int(11) default '0',\
  `added` datetime NOT NULL default '0000-00-00 00:00:00',\
  `edited` datetime default '0000-00-00 00:00:00',\
  `status` int(3) NOT NULL default '0',\
  `type` int(3) NOT NULL default '0',\
  `closedate` datetime default '0000-00-00 00:00:00',\
  `opendate` datetime default '0000-00-00 00:00:00',\
  `startdate` datetime default '0000-00-00 00:00:00',\
  `applyExternalUrl` varchar(250) default '',\
  `applyInternal` int(3) default '0',\
  `contactName` varchar(100) default '',\
  `contactEmail` varchar(100) default '',\
  `contactPhone` varchar(100) default '',\
  PRIMARY KEY  (`id`)\
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;\
\
CREATE TABLE `jos_jobs_employers` (\
  `id` int(11) NOT NULL auto_increment,\
  `uid` int(11) NOT NULL default '0',\
  `added` datetime NOT NULL default '0000-00-00 00:00:00',\
  `subscriptionid` int(11) NOT NULL default '0',\
  `companyName` varchar(250) default '',\
  `companyLocation` varchar(250) default '',\
  `companyWebsite` varchar(250) default '',\
  PRIMARY KEY  (`id`)\
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;\
\
CREATE TABLE `jos_jobs_categories` (\
  `id` int(11) NOT NULL auto_increment,\
  `category` varchar(150) NOT NULL default '',\
  `ordernum` int(11) NOT NULL default '0',\
  `description` varchar(255) default NULL,\
  PRIMARY KEY  (`id`)\
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;\
\
CREATE TABLE `jos_jobs_applications` (\
  `id` int(11) NOT NULL auto_increment,\
  `jid` int(11) NOT NULL default '0',\
  `uid` int(11) NOT NULL default '0',\
  `applied` datetime NOT NULL default '0000-00-00 00:00:00',\
  `withdrawn` datetime default '0000-00-00 00:00:00',\
  `cover` text,\
  `resumeid` int(11) default '0',\
  `status` int(11) default '1',\
  `reason` varchar(255) default '',\
  PRIMARY KEY  (`id`)\
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;\
\
CREATE TABLE `jos_jobs_admins` (\
  `id` int(11) NOT NULL auto_increment,\
  `jid` int(11) NOT NULL default '0',\
  `uid` int(11) NOT NULL default '0',\
  PRIMARY KEY  (`id`)\
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;\
\
}