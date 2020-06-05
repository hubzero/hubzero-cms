<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for installing newsfeeds tables
 **/
class Migration20180412000000ComNewsfeeds extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->deleteComponentEntry('newsfeeds');

		if ($this->db->tableExists('#__newsfeeds'))
		{
			$query = "DROP TABLE IF EXISTS `#__newsfeeds`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->addComponentEntry('newsfeeds');

		if (!$this->db->tableExists('#__newsfeeds'))
		{
			$query = "CREATE TABLE `#__newsfeeds` (
			  `catid` integer NOT NULL default '0',
			  `id` integer(10) UNSIGNED NOT NULL auto_increment,
			  `name`  varchar(100) NOT NULL DEFAULT '',
			  `alias` varchar(100) NOT NULL default '',
			  `link` varchar(200) NOT NULL DEFAULT '',
			  `filename` varchar(200) default NULL,
			  `published` tinyint(1) NOT NULL default '0',
			  `numarticles` integer unsigned NOT NULL default '1',
			  `cache_time` integer unsigned NOT NULL default '3600',
			  `checked_out` integer(10) unsigned NOT NULL default '0',
			  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
			  `ordering` integer NOT NULL default '0',
			  `rtl` tinyint(4) NOT NULL default '0',
			  `access` tinyint UNSIGNED NOT NULL DEFAULT '0',
			  `language` char(7) NOT NULL DEFAULT '',
			  `params` text NOT NULL,
			  `created` datetime NOT NULL default '0000-00-00 00:00:00',
			  `created_by` int(10) unsigned NOT NULL default '0',
			  `created_by_alias` varchar(255) NOT NULL default '',
			  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
			  `modified_by` int(10) unsigned NOT NULL default '0',
			  `metakey` text NOT NULL,
			  `metadesc` text NOT NULL,
			  `metadata` text NOT NULL,
			  `xreference` varchar(50) NOT NULL COMMENT 'A reference to enable linkages to external data sets.',
			  `publish_up` datetime NOT NULL default '0000-00-00 00:00:00',
			  `publish_down` datetime NOT NULL default '0000-00-00 00:00:00',

			  PRIMARY KEY  (`id`),
			  KEY `idx_access` (`access`),
			  KEY `idx_checkout` (`checked_out`),
			  KEY `idx_state` (`published`),
			  KEY `idx_catid` (`catid`),
			  KEY `idx_createdby` (`created_by`),
			  KEY `idx_language` (`language`),
			  KEY `idx_xreference` (`xreference`)

			)  DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
