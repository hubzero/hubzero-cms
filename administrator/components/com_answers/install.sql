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

CREATE TABLE IF NOT EXISTS `#__answers_questions_log` (
  `id` int(11) NOT NULL auto_increment,
  `qid` int(11) NOT NULL default '0',
  `expires` datetime NOT NULL default '0000-00-00 00:00:00',
  `voter` int(11) default NULL,
  `ip` varchar(15) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__answers_log` (
  `id` int(11) NOT NULL auto_increment,
  `rid` int(11) NOT NULL default '0',
  `ip` varchar(15) default NULL,
  `helpful` varchar(10) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

CREATE TABLE IF NOT EXISTS `#__answers_questions` (
  `id` int(11) NOT NULL auto_increment,
  `subject` varchar(250) default NULL,
  `question` text,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `created_by` varchar(50) default NULL,
  `state` tinyint(3) NOT NULL default '0',
  `anonymous`  tinyint(2) NOT NULL default '0',
  `email` tinyint(2) default '0',
  `helpful` int(11) NULL DEFAULT '0',
  `reward` tinyint(2) NULL DEFAULT '0',
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `question` (`question`),
  FULLTEXT KEY `subject` (`subject`)
) TYPE=MyISAM;
 
CREATE TABLE IF NOT EXISTS `#__answers_responses` (
  `id` int(11) NOT NULL auto_increment,
  `qid` int(11) NOT NULL default '0',
  `answer` text,
  `created_by` varchar(50) default NULL,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `helpful` int(11) NOT NULL default '0',
  `nothelpful` int(11) NOT NULL default '0',
  `state` tinyint(3) NOT NULL default '0',
  `anonymous` tinyint(2) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `answer` (`answer`)
) TYPE=MyISAM;

CREATE TABLE IF NOT EXISTS `#__answers_tags` (
  `id` int(11) NOT NULL auto_increment,
  `questionid` int(11) NOT NULL default '0',
  `tagid` int(11) NOT NULL default '0',
  `taggerid` varchar(200) default NULL,
  `taggedon` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

