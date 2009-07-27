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

CREATE TABLE IF NOT EXISTS `#__events` (
	`id` int(12) NOT NULL auto_increment,
	`sid` int(11) NOT NULL default '0',
	`catid` int(11) NOT NULL default '1',
	`title` varchar(100) NOT NULL default '',
	`content` longtext NOT NULL default '',
	`adresse_info` VARCHAR(120) NOT NULL default '',
	`contact_info` VARCHAR(120) NOT NULL default '',
	`extra_info` VARCHAR(240) NOT NULL default '',
	`color_bar` VARCHAR(8) NOT NULL default '',
	`useCatColor` TINYINT(1) NOT NULL default '0',
	`state` tinyint(3) NOT NULL default '0',
	`mask` int(11) unsigned NOT NULL default '0',
	`created` datetime NOT NULL default '0000-00-00 00:00:00',
	`created_by` int(11) unsigned NOT NULL default '0',
	`created_by_alias` varchar(100) NOT NULL default '',
	`modified` datetime NOT NULL default '0000-00-00 00:00:00',
	`modified_by` int(11) unsigned NOT NULL default '0',
	`checked_out` int(11) unsigned NOT NULL default '0',
	`checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
	`publish_up` datetime NOT NULL default '0000-00-00 00:00:00',
	`publish_down` datetime NOT NULL default '0000-00-00 00:00:00',
	`images` text NOT NULL default '',
	`reccurtype` tinyint(1) NOT NULL default '0',
	`reccurday` varchar(4) NOT NULL default '',
	`reccurweekdays` varchar(20) NOT NULL default '',
	`reccurweeks` varchar(10) NOT NULL default '',
	`approved` tinyint(1) NOT NULL default '1',
	`announcement` tinyint(1) NOT NULL default '0',
	`ordering` int(11) NOT NULL default '0',
	`archived` tinyint(1) NOT NULL default '0',
	`access` int(11) unsigned NOT NULL default '0',
	`hits` int(11) NOT NULL default '0',
	PRIMARY KEY  (`id`),
	FULLTEXT KEY `title` (`title`),
	FULLTEXT KEY `content` (`content`)
) TYPE=MyISAM; 

CREATE TABLE IF NOT EXISTS `#__events_categories` (
	`id` INT(12) NOT NULL default '0' PRIMARY KEY,
	`color` VARCHAR(8) NOT NULL default''
) TYPE=MyISAM;

CREATE TABLE IF NOT EXISTS `#__events_config` (
  `param` varchar(100) default NULL,
  `value` tinytext
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

