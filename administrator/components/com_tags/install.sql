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

# table #__tags
# ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `#__tags` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tag` varchar(100) NOT NULL DEFAULT '',
  `raw_tag` varchar(100) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `admin` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_tag` (`tag`),
  FULLTEXT KEY `ftidx_raw_tag_description` (`raw_tag`,`description`),
  FULLTEXT KEY `ftidx_description` (`description`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# table #__tags_log
# ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `#__tags_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tag_id` int(11) unsigned NOT NULL DEFAULT '0',
  `timestamp` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `action` varchar(50) NOT NULL DEFAULT '',
  `comments` text NOT NULL,
  `actorid` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_tag_id` (`tag_id`),
  KEY `idx_user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# table #__tags_object
# ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `#__tags_object` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `objectid` int(11) unsigned NOT NULL DEFAULT '0',
  `tagid` int(11) unsigned NOT NULL DEFAULT '0',
  `strength` tinyint(3) NOT NULL DEFAULT '0',
  `taggerid` int(11) unsigned NOT NULL DEFAULT '0',
  `taggedon` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `tbl` varchar(255) NOT NULL DEFAULT '',
  `label` varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_tagid` (`tagid`),
  KEY `idx_objectid_tbl` (`objectid`,`tbl`),
  KEY `idx_label_tagid` (`label`,`tagid`),
  KEY `idx_tbl_objectid_label_tagid` (`tbl`,`objectid`,`label`,`tagid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# table #__tags_substitute
# ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `#__tags_substitute` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tag_id` int(11) unsigned NOT NULL DEFAULT '0',
  `tag` varchar(100) NOT NULL DEFAULT '',
  `raw_tag` varchar(100) NOT NULL DEFAULT '',
  `created_by` int(11) unsigned NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `idx_tag_id` (`tag_id`),
  KEY `idx_created_by` (`created_by`),
  KEY `idx_tag` (`tag`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;