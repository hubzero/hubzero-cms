/**
 * @package      hubzero-cms
 * @file         administrator/components/com_tags/install.sql
 * @copyright    Copyright (c) 2005-2010 Purdue University. All rights reserved.
 * @license      http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 *
 * Copyright (c) 2005-2010 Purdue University
 * All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
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
