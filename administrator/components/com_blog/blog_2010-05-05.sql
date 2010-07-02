# Sequel Pro dump
# Version 2210
# http://code.google.com/p/sequel-pro
#
# Host: www3.nanohub.org (MySQL 5.0.51a-24+lenny3-log)
# Database: nanohub
# Generation Time: 2010-05-05 09:13:24 -0400
# ************************************************************

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table jos_blog_comments
# ------------------------------------------------------------

DROP TABLE IF EXISTS `jos_blog_comments`;

CREATE TABLE `jos_blog_comments` (
  `id` int(11) NOT NULL auto_increment,
  `entry_id` int(11) default '0',
  `content` text,
  `created` datetime default '0000-00-00 00:00:00',
  `created_by` int(11) default '0',
  `anonymous` tinyint(2) default '0',
  `parent` int(11) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;



# Dump of table jos_blog_entries
# ------------------------------------------------------------

DROP TABLE IF EXISTS `jos_blog_entries`;

CREATE TABLE `jos_blog_entries` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) default NULL,
  `alias` varchar(255) default NULL,
  `content` text,
  `created` datetime default '0000-00-00 00:00:00',
  `created_by` int(11) default '0',
  `state` tinyint(2) default '0',
  `publish_up` datetime default '0000-00-00 00:00:00',
  `publish_down` datetime default '0000-00-00 00:00:00',
  `params` tinytext,
  `group_id` int(11) default '0',
  `hits` int(11) default '0',
  `allow_comments` tinyint(2) default '0',
  `scope` varchar(100) default NULL,
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `title` (`title`),
  FULLTEXT KEY `content` (`content`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;






/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
