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

CREATE TABLE IF NOT EXISTS `#__tags` (
  `id` int(11) NOT NULL auto_increment,
  `tag` varchar(100) default NULL,
  `raw_tag` varchar(100) default NULL,
  `alias` varchar(100) default NULL,
  `description` text,
  `admin` tinyint(3) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `description` (`description`)
) TYPE=MyISAM DEFAULT CHARSET utf8;

CREATE TABLE IF NOT EXISTS `#__tags_object` (
  `id` int(11) NOT NULL auto_increment,
  `objectid` int(11) default NULL,
  `tagid` int(11) default NULL,
  `strength` tinyint(3) default '0',
  `taggerid` int(11) default '0',
  `taggedon` datetime default '0000-00-00 00:00:00',
  `tbl` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM DEFAULT CHARSET utf8;
