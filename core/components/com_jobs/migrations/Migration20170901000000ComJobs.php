<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for installing jobs tables
 **/
class Migration20170901000000ComJobs extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__jobs_admins'))
		{
			$query = "CREATE TABLE `#__jobs_admins` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `jid` int(11) NOT NULL DEFAULT '0',
			  `uid` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__jobs_applications'))
		{
			$query = "CREATE TABLE `#__jobs_applications` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `jid` int(11) NOT NULL DEFAULT '0',
			  `uid` int(11) NOT NULL DEFAULT '0',
			  `applied` datetime DEFAULT NULL,
			  `withdrawn` datetime DEFAULT NULL,
			  `cover` text,
			  `resumeid` int(11) DEFAULT '0',
			  `status` int(11) DEFAULT '1',
			  `reason` varchar(255) DEFAULT '',
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__jobs_categories'))
		{
			$query = "CREATE TABLE `#__jobs_categories` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `category` varchar(150) NOT NULL DEFAULT '',
			  `ordernum` int(11) NOT NULL DEFAULT '0',
			  `description` varchar(255) DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__jobs_employers'))
		{
			$query = "CREATE TABLE `#__jobs_employers` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `uid` int(11) NOT NULL DEFAULT '0',
			  `added` datetime DEFAULT NULL,
			  `subscriptionid` int(11) NOT NULL DEFAULT '0',
			  `companyName` varchar(250) DEFAULT '',
			  `companyLocation` varchar(250) DEFAULT '',
			  `companyWebsite` varchar(250) DEFAULT '',
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__jobs_openings'))
		{
			$query = "CREATE TABLE `#__jobs_openings` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `cid` int(11) DEFAULT '0',
			  `employerid` int(11) NOT NULL DEFAULT '0',
			  `code` int(11) NOT NULL DEFAULT '0',
			  `title` varchar(200) NOT NULL DEFAULT '',
			  `companyName` varchar(200) NOT NULL DEFAULT '',
			  `companyLocation` varchar(200) DEFAULT '',
			  `companyLocationCountry` varchar(100) DEFAULT '',
			  `companyWebsite` varchar(200) DEFAULT '',
			  `description` text,
			  `addedBy` int(11) NOT NULL DEFAULT '0',
			  `editedBy` int(11) DEFAULT '0',
			  `added` datetime DEFAULT NULL,
			  `edited` datetime DEFAULT NULL,
			  `status` int(3) NOT NULL DEFAULT '0',
			  `type` int(3) NOT NULL DEFAULT '0',
			  `closedate` datetime DEFAULT NULL,
			  `expiredate` datetime DEFAULT NULL,
			  `opendate` datetime DEFAULT NULL,
			  `startdate` datetime DEFAULT NULL,
			  `applyExternalUrl` varchar(250) DEFAULT '',
			  `applyInternal` int(3) DEFAULT '0',
			  `contactName` varchar(100) DEFAULT '',
			  `contactEmail` varchar(100) DEFAULT '',
			  `contactPhone` varchar(100) DEFAULT '',
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__jobs_prefs'))
		{
			$query = "CREATE TABLE `#__jobs_prefs` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `uid` int(10) NOT NULL DEFAULT '0',
			  `category` varchar(20) NOT NULL DEFAULT 'resume',
			  `filters` text,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__jobs_resumes'))
		{
			$query = "CREATE TABLE `#__jobs_resumes` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `uid` int(11) NOT NULL DEFAULT '0',
			  `created` datetime DEFAULT NULL,
			  `title` varchar(100) DEFAULT NULL,
			  `filename` varchar(100) DEFAULT NULL,
			  `main` tinyint(2) DEFAULT '1',
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__jobs_seekers'))
		{
			$query = "CREATE TABLE `#__jobs_seekers` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `uid` int(11) NOT NULL DEFAULT '0',
			  `active` int(11) NOT NULL DEFAULT '0',
			  `lookingfor` varchar(255) DEFAULT '',
			  `tagline` varchar(255) DEFAULT '',
			  `linkedin` varchar(255) DEFAULT '',
			  `url` varchar(255) DEFAULT '',
			  `updated` datetime DEFAULT NULL,
			  `sought_cid` int(11) DEFAULT '0',
			  `sought_type` int(11) DEFAULT '0',
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__jobs_shortlist'))
		{
			$query = "CREATE TABLE `#__jobs_shortlist` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `emp` int(11) NOT NULL DEFAULT '0',
			  `seeker` int(11) NOT NULL DEFAULT '0',
			  `category` varchar(11) NOT NULL DEFAULT 'resume',
			  `jobid` int(11) DEFAULT '0',
			  `added` datetime DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__jobs_stats'))
		{
			$query = "CREATE TABLE `#__jobs_stats` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `itemid` int(11) NOT NULL,
			  `category` varchar(11) NOT NULL DEFAULT '',
			  `total_viewed` int(11) DEFAULT '0',
			  `total_shared` int(11) DEFAULT '0',
			  `viewed_today` int(11) DEFAULT '0',
			  `lastviewed` datetime DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__jobs_types'))
		{
			$query = "CREATE TABLE `#__jobs_types` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `category` varchar(150) NOT NULL DEFAULT '',
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__jobs_admins'))
		{
			$query = "DROP TABLE IF EXISTS `#__jobs_admins`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__jobs_applications'))
		{
			$query = "DROP TABLE IF EXISTS `#__jobs_applications`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__jobs_categories'))
		{
			$query = "DROP TABLE IF EXISTS `#__jobs_categories`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__jobs_employers'))
		{
			$query = "DROP TABLE IF EXISTS `#__jobs_employers`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__jobs_openings'))
		{
			$query = "DROP TABLE IF EXISTS `#__jobs_openings`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__jobs_prefs'))
		{
			$query = "DROP TABLE IF EXISTS `#__jobs_prefs`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__jobs_resumes'))
		{
			$query = "DROP TABLE IF EXISTS `#__jobs_resumes`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__jobs_seekers'))
		{
			$query = "DROP TABLE IF EXISTS `#__jobs_seekers`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__jobs_shortlist'))
		{
			$query = "DROP TABLE IF EXISTS `#__jobs_shortlist`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__jobs_stats'))
		{
			$query = "DROP TABLE IF EXISTS `#__jobs_stats`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__jobs_types'))
		{
			$query = "DROP TABLE IF EXISTS `#__jobs_types`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
