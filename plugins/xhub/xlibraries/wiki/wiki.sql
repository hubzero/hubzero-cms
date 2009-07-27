/**
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

# Dump of table #__wiki_attachments
# ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `#__wiki_attachments` (
  `id` int(11) NOT NULL auto_increment,
  `pageid` int(11) default '0',
  `filename` varchar(255) default NULL,
  `description` tinytext,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__wiki_log` (
	id       	int(11) AUTO_INCREMENT NOT NULL,
	pid      	int(11) NOT NULL DEFAULT '0',
	timestamp	datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	uid      	int(11) NULL DEFAULT '0',
	action   	varchar(50) NULL,
	comments 	text NULL,
	actorid  	int(11) NULL DEFAULT '0',
	PRIMARY KEY(id)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


# Dump of table #__wiki_comments
# ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `#__wiki_comments` (
  `id` int(11) NOT NULL auto_increment,
  `pageid` int(11) NOT NULL default '0',
  `version` int(11) NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL default '0',
  `ctext` text,
  `chtml` text,
  `rating` tinyint(1) NOT NULL default '0',
  `anonymous` tinyint(1) NOT NULL default '0',
  `parent` int(11) NOT NULL default '0',
  `status` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;



# Dump of table #__wiki_math
# ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `#__wiki_math` (
  `inputhash` varbinary(16) NOT NULL,
  `outputhash` varbinary(16) NOT NULL,
  `conservativeness` tinyint(4) NOT NULL,
  `html` text,
  `mathml` text,
  `id` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `inputhash` (`inputhash`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table #__wiki_page
# ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `#__wiki_page` (
  `id` int(11) NOT NULL auto_increment,
  `pagename` varchar(100) default NULL,
  `hits` int(11) NOT NULL default '0',
  `created_by` int(11) NOT NULL default '0',
  `rating` decimal(2,1) NOT NULL default '0.0',
  `times_rated` int(11) NOT NULL default '0',
  `title` varchar(255) default NULL,
  `scope` varchar(255) NOT NULL,
  `params` tinytext,
  `ranking` float default '0',
  `authors` varchar(255) default NULL,
  `access` tinyint(2) default '0',
  `group` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `title` (`title`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;



# Dump of table #__wiki_version
# ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `#__wiki_version` (
  `id` int(11) NOT NULL auto_increment,
  `pageid` int(11) NOT NULL default '0',
  `version` int(11) NOT NULL default '0',
  `created` datetime default NULL,
  `created_by` int(11) NOT NULL default '0',
  `minor_edit` int(1) NOT NULL default '0',
  `pagetext` text,
  `pagehtml` text,
  `approved` int(1) NOT NULL default '0',
  `summary` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `pagetext` (`pagetext`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


