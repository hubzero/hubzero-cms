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
 * Migration script for installing categories tables
 **/
class Migration20170901000000ComCategories extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__categories'))
		{
			$query = "CREATE TABLE `#__categories` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `asset_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'FK to the #__assets table.',
			  `parent_id` int(10) unsigned NOT NULL DEFAULT '0',
			  `lft` int(11) NOT NULL DEFAULT '0',
			  `rgt` int(11) NOT NULL DEFAULT '0',
			  `level` int(10) unsigned NOT NULL DEFAULT '0',
			  `path` varchar(255) NOT NULL DEFAULT '',
			  `extension` varchar(50) NOT NULL DEFAULT '',
			  `title` varchar(255) NOT NULL DEFAULT '',
			  `alias` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
			  `note` varchar(255) NOT NULL DEFAULT '',
			  `description` mediumtext NOT NULL,
			  `published` tinyint(1) NOT NULL DEFAULT '0',
			  `checked_out` int(11) unsigned NOT NULL DEFAULT '0',
			  `checked_out_time` datetime DEFAULT NULL,
			  `access` int(10) unsigned NOT NULL DEFAULT '0',
			  `params` text NOT NULL,
			  `metadesc` varchar(1024) NOT NULL COMMENT 'The meta description for the page.',
			  `metakey` varchar(1024) NOT NULL COMMENT 'The meta keywords for the page.',
			  `metadata` varchar(2048) NOT NULL COMMENT 'JSON encoded metadata properties.',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '0',
			  `created_time` datetime DEFAULT NULL,
			  `modified_user_id` int(10) unsigned NOT NULL DEFAULT '0',
			  `modified_time` datetime DEFAULT NULL,
			  `hits` int(10) unsigned NOT NULL DEFAULT '0',
			  `language` char(7) NOT NULL,
			  PRIMARY KEY (`id`),
			  KEY `idx_extension_published_access` (`extension`,`published`,`access`),
			  KEY `idx_access` (`access`),
			  KEY `idx_checkout` (`checked_out`),
			  KEY `idx_path` (`path`),
			  KEY `idx_left_right` (`lft`,`rgt`),
			  KEY `idx_alias` (`alias`),
			  KEY `idx_language` (`language`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();

			$dt = new \Hubzero\Utility\Date();

			$query = "INSERT INTO `#__categories`
					VALUES (
						1,
						0,
						0,
						0,
						1,
						0,
						'',
						'system',
						'ROOT',
						'root',
						'',
						'',
						1,
						0,
						null,
						1,
						'{}',
						'',
						'',
						'',
						0,
						" . $this->db->quote($dt->toSql()) . ",
						0,
						null,
						0,
						'*'
					)";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__categories'))
		{
			$query = "DROP TABLE IF EXISTS `#__categories`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
