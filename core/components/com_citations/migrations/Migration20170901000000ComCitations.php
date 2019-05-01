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
 * Migration script for installing citations tables
 **/
class Migration20170901000000ComCitations extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__citations'))
		{
			$query = "CREATE TABLE `#__citations` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `uid` int(11) DEFAULT NULL,
			  `type` varchar(30) DEFAULT NULL,
			  `published` int(3) NOT NULL DEFAULT '1',
			  `affiliated` int(11) NOT NULL DEFAULT '0',
			  `fundedby` int(3) DEFAULT NULL,
			  `created` datetime DEFAULT NULL,
			  `address` varchar(250) DEFAULT NULL,
			  `author` text,
			  `booktitle` varchar(250) DEFAULT NULL,
			  `chapter` varchar(250) DEFAULT NULL,
			  `cite` varchar(250) DEFAULT NULL,
			  `edition` varchar(250) DEFAULT NULL,
			  `editor` varchar(250) DEFAULT NULL,
			  `eprint` varchar(250) DEFAULT NULL,
			  `howpublished` varchar(250) DEFAULT NULL,
			  `institution` varchar(250) DEFAULT NULL,
			  `isbn` varchar(50) DEFAULT NULL,
			  `journal` varchar(250) DEFAULT NULL,
			  `key` varchar(250) DEFAULT NULL,
			  `location` varchar(250) DEFAULT NULL,
			  `month` varchar(50) DEFAULT NULL,
			  `note` text,
			  `number` varchar(50) DEFAULT NULL,
			  `organization` varchar(250) DEFAULT NULL,
			  `pages` varchar(250) DEFAULT NULL,
			  `publisher` varchar(250) DEFAULT NULL,
			  `series` varchar(250) DEFAULT NULL,
			  `school` varchar(250) DEFAULT NULL,
			  `title` varchar(250) DEFAULT NULL,
			  `url` varchar(250) DEFAULT NULL,
			  `volume` varchar(11) DEFAULT NULL,
			  `year` varchar(4) DEFAULT NULL,
			  `doi` varchar(250) DEFAULT NULL,
			  `ref_type` varchar(50) DEFAULT NULL,
			  `date_submit` datetime DEFAULT NULL,
			  `date_accept` datetime DEFAULT NULL,
			  `date_publish` datetime DEFAULT NULL,
			  `software_use` int(3) DEFAULT NULL,
			  `res_edu` int(3) DEFAULT NULL,
			  `exp_list_exp_data` int(3) DEFAULT NULL,
			  `exp_data` int(3) DEFAULT NULL,
			  `notes` text,
			  `language` varchar(100) DEFAULT NULL,
			  `accession_number` varchar(100) DEFAULT NULL,
			  `short_title` varchar(250) DEFAULT NULL,
			  `author_address` text,
			  `keywords` text,
			  `abstract` text,
			  `call_number` varchar(100) DEFAULT NULL,
			  `label` varchar(100) DEFAULT NULL,
			  `research_notes` text,
			  `params` text,
			  `formatted` text,
			  `format` varchar(11) DEFAULT NULL,
			  `scope` varchar(45) DEFAULT NULL,
			  `scope_id` varchar(45) DEFAULT NULL,
			  `custom1` text,
			  `custom2` text,
			  `custom3` varchar(45) DEFAULT NULL,
			  `custom4` varchar(45) DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  FULLTEXT KEY `ftidx_title_isbn_doi_abstract` (`title`,`isbn`,`doi`,`abstract`),
			  FULLTEXT KEY `ftidx_title_isbn_doi_abstract_author_publisher` (`title`,`isbn`,`doi`,`abstract`,`author`,`publisher`),
			  FULLTEXT KEY `ftidx_search` (`title`,`isbn`,`doi`,`abstract`,`author`,`publisher`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__citations_assoc'))
		{
			$query = "CREATE TABLE `#__citations_assoc` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `cid` int(11) DEFAULT '0',
			  `oid` int(11) DEFAULT '0',
			  `type` varchar(50) DEFAULT NULL,
			  `tbl` varchar(50) DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__citations_authors'))
		{
			$query = "CREATE TABLE `#__citations_authors` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `cid` int(11) DEFAULT '0',
			  `author` varchar(64) DEFAULT NULL,
			  `authorid` int(11) DEFAULT '0',
			  `uidNumber` int(11) DEFAULT '0',
			  `ordering` int(11) NOT NULL DEFAULT '0',
			  `givenName` varchar(255) NOT NULL DEFAULT '',
			  `middleName` varchar(255) NOT NULL DEFAULT '',
			  `surname` varchar(255) NOT NULL DEFAULT '',
			  `organization` varchar(255) NOT NULL DEFAULT '',
			  `org_dept` varchar(255) NOT NULL DEFAULT '',
			  `orgtype` varchar(255) NOT NULL DEFAULT '',
			  `countryresident` char(2) NOT NULL DEFAULT '',
			  `email` varchar(100) NOT NULL DEFAULT '',
			  `ip` varchar(40) NOT NULL DEFAULT '',
			  `host` varchar(64) NOT NULL DEFAULT '',
			  `countrySHORT` char(2) NOT NULL DEFAULT '',
			  `countryLONG` varchar(64) NOT NULL DEFAULT '',
			  `ipREGION` varchar(128) NOT NULL DEFAULT '',
			  `ipCITY` varchar(128) NOT NULL DEFAULT '',
			  `ipLATITUDE` double DEFAULT NULL,
			  `ipLONGITUDE` double DEFAULT NULL,
			  `in_network` int(1) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `uidx_cid_author_authorid_uidNumber` (`cid`,`author`,`authorid`,`uidNumber`),
			  KEY `idx_authorid` (`authorid`),
			  KEY `idx_uidNumber` (`uidNumber`),
			  KEY `idx_cid` (`cid`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__citations_format'))
		{
			$query = "CREATE TABLE `#__citations_format` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `typeid` int(11) DEFAULT NULL,
			  `style` varchar(50) DEFAULT NULL,
			  `format` text,
			  PRIMARY KEY (`id`),
			  KEY `idx_typeid` (`typeid`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__citations_links'))
		{
			$query = "CREATE TABLE `#__citations_links` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `title` varchar(255) NOT NULL DEFAULT '',
			  `url` text,
			  `citation_id` int(11) unsigned NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_citation_id` (`citation_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__citations_secondary'))
		{
			$query = "CREATE TABLE `#__citations_secondary` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `cid` int(11) NOT NULL,
			  `sec_cits_cnt` int(11) DEFAULT NULL,
			  `search_string` tinytext,
			  `scope` varchar(250) DEFAULT NULL,
			  `scope_id` int(11) DEFAULT NULL,
			  `link1_url` tinytext,
			  `link1_title` varchar(60) DEFAULT NULL,
			  `link2_url` tinytext,
			  `link2_title` varchar(60) DEFAULT NULL,
			  `link3_url` tinytext,
			  `link3_title` varchar(60) DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `idx_cid` (`cid`),
			  KEY `idx_scope_scope_id` (`scope`,`scope_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__citations_sponsors'))
		{
			$query = "CREATE TABLE `#__citations_sponsors` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `sponsor` varchar(150) DEFAULT NULL,
			  `link` varchar(200) DEFAULT NULL,
			  `image` varchar(200) DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__citations_sponsors_assoc'))
		{
			$query = "CREATE TABLE `#__citations_sponsors_assoc` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `cid` int(11) DEFAULT NULL,
			  `sid` int(11) DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__citations_types'))
		{
			$query = "CREATE TABLE `#__citations_types` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `type` varchar(255) DEFAULT NULL,
			  `type_title` varchar(255) DEFAULT NULL,
			  `type_desc` text,
			  `type_export` varchar(255) DEFAULT NULL,
			  `fields` text,
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
		if ($this->db->tableExists('#__citations'))
		{
			$query = "DROP TABLE IF EXISTS `#__citations`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__citations_assoc'))
		{
			$query = "DROP TABLE IF EXISTS `#__citations_assoc`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__citations_authors'))
		{
			$query = "DROP TABLE IF EXISTS `#__citations_authors`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__citations_format'))
		{
			$query = "DROP TABLE IF EXISTS `#__citations_format`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__citations_links'))
		{
			$query = "DROP TABLE IF EXISTS `#__citations_links`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__citations_secondary'))
		{
			$query = "DROP TABLE IF EXISTS `#__citations_secondary`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__citations_sponsors'))
		{
			$query = "DROP TABLE IF EXISTS `#__citations_sponsors`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__citations_sponsors_assoc'))
		{
			$query = "DROP TABLE IF EXISTS `#__citations_sponsors_assoc`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__citations_types'))
		{
			$query = "DROP TABLE IF EXISTS `#__citations_types`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
