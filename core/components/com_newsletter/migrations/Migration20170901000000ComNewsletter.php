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
 * Migration script for installing newsletter tables
 **/
class Migration20170901000000ComNewsletter extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__newsletter_mailing_recipient_actions'))
		{
			$query = "CREATE TABLE `#__newsletter_mailing_recipient_actions` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `mailingid` int(11) DEFAULT NULL,
			  `action` varchar(100) DEFAULT NULL,
			  `action_vars` text,
			  `email` varchar(255) DEFAULT NULL,
			  `ip` varchar(100) DEFAULT NULL,
			  `user_agent` varchar(255) DEFAULT NULL,
			  `date` datetime DEFAULT NULL,
			  `countrySHORT` char(2) DEFAULT NULL,
			  `countryLONG` varchar(64) DEFAULT NULL,
			  `ipREGION` varchar(128) DEFAULT NULL,
			  `ipCITY` varchar(128) DEFAULT NULL,
			  `ipLATITUDE` double DEFAULT NULL,
			  `ipLONGITUDE` double DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `idx_mailingid` (`mailingid`),
			  KEY `idx_action` (`action`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__newsletter_mailing_recipients'))
		{
			$query = "CREATE TABLE `#__newsletter_mailing_recipients` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `mid` int(11) DEFAULT NULL,
			  `email` varchar(150) DEFAULT NULL,
			  `status` varchar(100) DEFAULT NULL,
			  `date_added` datetime DEFAULT NULL,
			  `date_sent` datetime DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `idx_mid` (`mid`),
			  KEY `idx_status` (`status`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__newsletter_mailinglist_emails'))
		{
			$query = "CREATE TABLE `#__newsletter_mailinglist_emails` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `mid` int(11) DEFAULT NULL,
			  `email` varchar(150) DEFAULT NULL,
			  `status` varchar(100) DEFAULT NULL,
			  `confirmed` int(11) DEFAULT '0',
			  `date_added` datetime DEFAULT NULL,
			  `date_confirmed` datetime DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `idx_mid` (`mid`),
			  KEY `idx_status` (`status`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__newsletter_mailinglists'))
		{
			$query = "CREATE TABLE `#__newsletter_mailinglists` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `name` varchar(150) DEFAULT NULL,
			  `description` text,
			  `private` int(11) DEFAULT NULL,
			  `deleted` int(11) DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_private` (`private`),
			  KEY `idx_deleted` (`deleted`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__newsletter_mailings'))
		{
			$query = "CREATE TABLE `#__newsletter_mailings` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `nid` int(11) DEFAULT NULL,
			  `lid` int(11) DEFAULT NULL,
			  `subject` varchar(250) DEFAULT NULL,
			  `html_body` longtext,
			  `plain_body` longtext,
			  `headers` text,
			  `args` text,
			  `tracking` int(11) DEFAULT '1',
			  `date` datetime DEFAULT NULL,
			  `deleted` int(11) DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_nid` (`nid`),
			  KEY `idx_lid` (`lid`),
			  KEY `idx_deleted` (`deleted`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__newsletter_primary_story'))
		{
			$query = "CREATE TABLE `#__newsletter_primary_story` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `nid` int(11) NOT NULL,
			  `title` varchar(150) DEFAULT NULL,
			  `story` text,
			  `readmore_title` varchar(100) DEFAULT NULL,
			  `readmore_link` varchar(200) DEFAULT NULL,
			  `order` int(11) DEFAULT NULL,
			  `deleted` int(11) DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_nid` (`nid`),
			  KEY `idx_deleted` (`deleted`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__newsletter_secondary_story'))
		{
			$query = "CREATE TABLE `#__newsletter_secondary_story` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `nid` int(11) NOT NULL,
			  `title` varchar(150) DEFAULT NULL,
			  `story` text,
			  `readmore_title` varchar(100) DEFAULT NULL,
			  `readmore_link` varchar(200) DEFAULT NULL,
			  `order` int(11) DEFAULT NULL,
			  `deleted` int(11) DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_nid` (`nid`),
			  KEY `idx_deleted` (`deleted`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__newsletter_templates'))
		{
			$query = "CREATE TABLE `#__newsletter_templates` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `editable` int(11) DEFAULT '1',
			  `name` varchar(100) DEFAULT NULL,
			  `template` text,
			  `primary_title_color` varchar(255) DEFAULT NULL,
			  `primary_text_color` varchar(255) DEFAULT NULL,
			  `secondary_title_color` varchar(255) DEFAULT NULL,
			  `secondary_text_color` varchar(255) DEFAULT NULL,
			  `deleted` int(11) DEFAULT '0',
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__newsletters'))
		{
			$query = "CREATE TABLE `#__newsletters` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `alias` varchar(150) DEFAULT NULL,
			  `name` varchar(150) DEFAULT NULL,
			  `issue` int(11) DEFAULT NULL,
			  `type` varchar(50) DEFAULT 'html',
			  `template_id` int(11) DEFAULT NULL,
			  `published` int(11) DEFAULT '1',
			  `sent` int(11) DEFAULT '0',
			  `html_content` mediumtext,
			  `plain_content` mediumtext,
			  `tracking` int(11) DEFAULT '1',
			  `created` datetime DEFAULT NULL,
			  `created_by` int(11) DEFAULT NULL,
			  `modified` datetime DEFAULT NULL,
			  `modified_by` int(11) DEFAULT NULL,
			  `deleted` int(11) DEFAULT '0',
			  `params` text,
			  `autogen` int(11) DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_published` (`published`),
			  KEY `idx_sent` (`sent`),
			  KEY `idx_deleted` (`deleted`)
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
		if ($this->db->tableExists('#__newsletter_mailing_recipient_actions'))
		{
			$query = "DROP TABLE IF EXISTS `#__newsletter_mailing_recipient_actions`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__newsletter_mailing_recipients'))
		{
			$query = "DROP TABLE IF EXISTS `#__newsletter_mailing_recipients`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__newsletter_mailinglist_emails'))
		{
			$query = "DROP TABLE IF EXISTS `#__newsletter_mailinglist_emails`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__newsletter_mailinglist_unsubscribes'))
		{
			$query = "DROP TABLE IF EXISTS `#__newsletter_mailinglist_unsubscribes`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__newsletter_mailinglists'))
		{
			$query = "DROP TABLE IF EXISTS `#__newsletter_mailinglists`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__newsletter_mailings'))
		{
			$query = "DROP TABLE IF EXISTS `#__newsletter_mailings`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__newsletter_primary_story'))
		{
			$query = "DROP TABLE IF EXISTS `#__newsletter_primary_story`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__newsletter_secondary_story'))
		{
			$query = "DROP TABLE IF EXISTS `#__newsletter_secondary_story`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__newsletter_templates'))
		{
			$query = "DROP TABLE IF EXISTS `#__newsletter_templates`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__newsletters'))
		{
			$query = "DROP TABLE IF EXISTS `#__newsletters`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
