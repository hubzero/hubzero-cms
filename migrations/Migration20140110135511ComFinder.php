<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for ...
 **/
class Migration20140110135511ComFinder extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		$query = "SELECT `extension_id` FROM `#__extensions` WHERE `type`='component' AND `element`='com_finder';";

		$db->setQuery($query);

		if ($id = $db->loadResult())
		{
			self::deleteComponentEntry('finder');

			self::deleteModuleEntry('mod_finder');

			$query = "SELECT `id` FROM `#__modules` WHERE `module`='mod_finder';";
			$db->setQuery($query);
			if ($results = $db->loadResultArray())
			{
				$query = "DELETE FROM `#__modules_menu` WHERE `moduleid` IN (" . implode(',', $results) . ");";
				$db->setQuery($query);
				$db->query();

				$query = "DELETE FROM `#__modules` WHERE `module`='mod_finder';";
				$db->setQuery($query);
				$db->query();
			}

			self::deletePluginEntry('content', 'finder');

			self::deletePluginEntry('finder', 'categories');
			self::deletePluginEntry('finder', 'contacts');
			self::deletePluginEntry('finder', 'content');
			self::deletePluginEntry('finder', 'newsfeeds');
			self::deletePluginEntry('finder', 'weblinks');

			$query = "DROP TABLE IF EXISTS `#__finder_filters`;";
			$db->setQuery($query);
			$db->query();

			$query = "DROP TABLE IF EXISTS `#__finder_links`;";
			$db->setQuery($query);
			$db->query();

			$query = "DROP TABLE IF EXISTS `#__finder_links_terms0`;";
			$db->setQuery($query);
			$db->query();

			$query = "DROP TABLE IF EXISTS `#__finder_links_terms1`;";
			$db->setQuery($query);
			$db->query();

			$query = "DROP TABLE IF EXISTS `#__finder_links_terms2`;";
			$db->setQuery($query);
			$db->query();

			$query = "DROP TABLE IF EXISTS `#__finder_links_terms3`;";
			$db->setQuery($query);
			$db->query();

			$query = "DROP TABLE IF EXISTS `#__finder_links_terms4`;";
			$db->setQuery($query);
			$db->query();

			$query = "DROP TABLE IF EXISTS `#__finder_links_terms5`;";
			$db->setQuery($query);
			$db->query();

			$query = "DROP TABLE IF EXISTS `#__finder_links_terms6`;";
			$db->setQuery($query);
			$db->query();

			$query = "DROP TABLE IF EXISTS `#__finder_links_terms7`;";
			$db->setQuery($query);
			$db->query();

			$query = "DROP TABLE IF EXISTS `#__finder_links_terms8`;";
			$db->setQuery($query);
			$db->query();

			$query = "DROP TABLE IF EXISTS `#__finder_links_terms9`;";
			$db->setQuery($query);
			$db->query();

			$query = "DROP TABLE IF EXISTS `#__finder_links_termsa`;";
			$db->setQuery($query);
			$db->query();

			$query = "DROP TABLE IF EXISTS `#__finder_links_termsb`;";
			$db->setQuery($query);
			$db->query();

			$query = "DROP TABLE IF EXISTS `#__finder_links_termsc`;";
			$db->setQuery($query);
			$db->query();

			$query = "DROP TABLE IF EXISTS `#__finder_links_termsd`;";
			$db->setQuery($query);
			$db->query();

			$query = "DROP TABLE IF EXISTS `#__finder_links_termse`;";
			$db->setQuery($query);
			$db->query();

			$query = "DROP TABLE IF EXISTS `#__finder_links_termsf`;";
			$db->setQuery($query);
			$db->query();

			$query = "DROP TABLE IF EXISTS `#__finder_taxonomy`;";
			$db->setQuery($query);
			$db->query();

			$query = "DROP TABLE IF EXISTS `#__finder_taxonomy_map`;";
			$db->setQuery($query);
			$db->query();

			$query = "DROP TABLE IF EXISTS `#__finder_terms`;";
			$db->setQuery($query);
			$db->query();

			$query = "DROP TABLE IF EXISTS `#__finder_terms_common`;";
			$db->setQuery($query);
			$db->query();

			$query = "DROP TABLE IF EXISTS `#__finder_tokens`;";
			$db->setQuery($query);
			$db->query();

			$query = "DROP TABLE IF EXISTS `#__finder_tokens_aggregate`;";
			$db->setQuery($query);
			$db->query();

			$query = "DROP TABLE IF EXISTS `#__finder_types`;";
			$db->setQuery($query);
			$db->query();
		}
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		$query = "SELECT `extension_id` FROM `#__extensions` WHERE `type`='component' AND `element`='com_finder';";

		$db->setQuery($query);

		if (!($id = $db->loadResult()))
		{
			self::addComponentEntry('finder');

			self::addPluginEntry('content', 'finder', 0);

			self::addPluginEntry('finder', 'categories', 0);
			self::addPluginEntry('finder', 'contacts', 0);
			self::addPluginEntry('finder', 'content', 0);
			self::addPluginEntry('finder', 'newsfeeds', 0);
			self::addPluginEntry('finder', 'weblinks', 0);

			if (!$db->tableExists('#__finder_details'))
			{
				$query = "CREATE TABLE `#__finder_filters` (
					  `filter_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
					  `title` varchar(255) NOT NULL,
					  `alias` varchar(255) NOT NULL,
					  `state` tinyint(1) NOT NULL DEFAULT '1',
					  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
					  `created_by` int(10) unsigned NOT NULL,
					  `created_by_alias` varchar(255) NOT NULL,
					  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
					  `modified_by` int(10) unsigned NOT NULL DEFAULT '0',
					  `checked_out` int(10) unsigned NOT NULL DEFAULT '0',
					  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
					  `map_count` int(10) unsigned NOT NULL DEFAULT '0',
					  `data` text NOT NULL,
					  `params` mediumtext,
					  PRIMARY KEY (`filter_id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
				$db->setQuery($query);
				$db->query();
			}

			if (!$db->tableExists('#__finder_links'))
			{
				$query = "CREATE TABLE `#__finder_links` (
					  `link_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
					  `url` varchar(255) NOT NULL,
					  `route` varchar(255) NOT NULL,
					  `title` varchar(255) DEFAULT NULL,
					  `description` varchar(255) DEFAULT NULL,
					  `indexdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
					  `md5sum` varchar(32) DEFAULT NULL,
					  `published` tinyint(1) NOT NULL DEFAULT '1',
					  `state` int(5) DEFAULT '1',
					  `access` int(5) DEFAULT '0',
					  `language` varchar(8) NOT NULL,
					  `publish_start_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
					  `publish_end_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
					  `start_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
					  `end_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
					  `list_price` double unsigned NOT NULL DEFAULT '0',
					  `sale_price` double unsigned NOT NULL DEFAULT '0',
					  `type_id` int(11) NOT NULL,
					  `object` mediumblob NOT NULL,
					  PRIMARY KEY (`link_id`),
					  KEY `idx_type` (`type_id`),
					  KEY `idx_title` (`title`),
					  KEY `idx_md5` (`md5sum`),
					  KEY `idx_url` (`url`(75)),
					  KEY `idx_published_list` (`published`,`state`,`access`,`publish_start_date`,`publish_end_date`,`list_price`),
					  KEY `idx_published_sale` (`published`,`state`,`access`,`publish_start_date`,`publish_end_date`,`sale_price`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
				$db->setQuery($query);
				$db->query();
			}

			for ($i = 0; $i < 10; $i++)
			{
				if (!$db->tableExists('#__finder_links_terms' . $i))
				{
					$query = "CREATE TABLE `#__finder_links_terms$i` (
						  `link_id` int(10) unsigned NOT NULL,
						  `term_id` int(10) unsigned NOT NULL,
						  `weight` float unsigned NOT NULL,
						  PRIMARY KEY (`link_id`,`term_id`),
						  KEY `idx_term_weight` (`term_id`,`weight`),
						  KEY `idx_link_term_weight` (`link_id`,`term_id`,`weight`)
						) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
					$db->setQuery($query);
					$db->query();
				}
			}

			$alpha = array('a', 'b', 'c', 'd', 'e', 'f');

			foreach ($alpha as $beta)
			{
				if (!$db->tableExists('#__finder_links_terms' . $beta))
				{
					$query = "CREATE TABLE `#__finder_links_terms$beta` (
						  `link_id` int(10) unsigned NOT NULL,
						  `term_id` int(10) unsigned NOT NULL,
						  `weight` float unsigned NOT NULL,
						  PRIMARY KEY (`link_id`,`term_id`),
						  KEY `idx_term_weight` (`term_id`,`weight`),
						  KEY `idx_link_term_weight` (`link_id`,`term_id`,`weight`)
						) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
					$db->setQuery($query);
					$db->query();
				}
			}

			if (!$db->tableExists('#__finder_taxonomy'))
			{
				$query = "CREATE TABLE `#__finder_taxonomy` (
					  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
					  `parent_id` int(10) unsigned NOT NULL DEFAULT '0',
					  `title` varchar(255) NOT NULL,
					  `state` tinyint(1) unsigned NOT NULL DEFAULT '1',
					  `access` tinyint(1) unsigned NOT NULL DEFAULT '0',
					  `ordering` tinyint(1) unsigned NOT NULL DEFAULT '0',
					  PRIMARY KEY (`id`),
					  KEY `parent_id` (`parent_id`),
					  KEY `state` (`state`),
					  KEY `ordering` (`ordering`),
					  KEY `access` (`access`),
					  KEY `idx_parent_published` (`parent_id`,`state`,`access`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
				$db->setQuery($query);
				$db->query();
			}

			if (!$db->tableExists('#__finder_taxonomy_map'))
			{
				$query = "CREATE TABLE `#__finder_taxonomy_map` (
					  `link_id` int(10) unsigned NOT NULL,
					  `node_id` int(10) unsigned NOT NULL,
					  PRIMARY KEY (`link_id`,`node_id`),
					  KEY `link_id` (`link_id`),
					  KEY `node_id` (`node_id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
				$db->setQuery($query);
				$db->query();
			}

			if (!$db->tableExists('#__finder_terms'))
			{
				$query = "CREATE TABLE `#__finder_terms` (
					  `term_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
					  `term` varchar(75) NOT NULL,
					  `stem` varchar(75) NOT NULL,
					  `common` tinyint(1) unsigned NOT NULL DEFAULT '0',
					  `phrase` tinyint(1) unsigned NOT NULL DEFAULT '0',
					  `weight` float unsigned NOT NULL DEFAULT '0',
					  `soundex` varchar(75) NOT NULL,
					  `links` int(10) NOT NULL DEFAULT '0',
					  PRIMARY KEY (`term_id`),
					  UNIQUE KEY `idx_term` (`term`),
					  KEY `idx_term_phrase` (`term`,`phrase`),
					  KEY `idx_stem_phrase` (`stem`,`phrase`),
					  KEY `idx_soundex_phrase` (`soundex`,`phrase`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
				$db->setQuery($query);
				$db->query();
			}

			if (!$db->tableExists('#__finder_terms_common'))
			{
				$query = "CREATE TABLE `#__finder_terms_common` (
					  `term` varchar(75) NOT NULL,
					  `language` varchar(3) NOT NULL,
					  KEY `idx_word_lang` (`term`,`language`),
					  KEY `idx_lang` (`language`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
				$db->setQuery($query);
				$db->query();
			}

			if (!$db->tableExists('#__finder_tokens'))
			{
				$query = "CREATE TABLE `#__finder_tokens` (
					  `term` varchar(75) NOT NULL,
					  `stem` varchar(75) NOT NULL,
					  `common` tinyint(1) unsigned NOT NULL DEFAULT '0',
					  `phrase` tinyint(1) unsigned NOT NULL DEFAULT '0',
					  `weight` float unsigned NOT NULL DEFAULT '1',
					  `context` tinyint(1) unsigned NOT NULL DEFAULT '2',
					  KEY `idx_word` (`term`),
					  KEY `idx_context` (`context`)
					) ENGINE=MEMORY DEFAULT CHARSET=utf8;";
				$db->setQuery($query);
				$db->query();
			}

			if (!$db->tableExists('#__finder_tokens_aggregate'))
			{
				$query = "CREATE TABLE `#__finder_tokens_aggregate` (
					  `term_id` int(10) unsigned NOT NULL,
					  `map_suffix` char(1) NOT NULL,
					  `term` varchar(75) NOT NULL,
					  `stem` varchar(75) NOT NULL,
					  `common` tinyint(1) unsigned NOT NULL DEFAULT '0',
					  `phrase` tinyint(1) unsigned NOT NULL DEFAULT '0',
					  `term_weight` float unsigned NOT NULL,
					  `context` tinyint(1) unsigned NOT NULL DEFAULT '2',
					  `context_weight` float unsigned NOT NULL,
					  `total_weight` float unsigned NOT NULL,
					  KEY `token` (`term`),
					  KEY `keyword_id` (`term_id`)
					) ENGINE=MEMORY DEFAULT CHARSET=utf8;";
				$db->setQuery($query);
				$db->query();
			}

			if (!$db->tableExists('#__finder_types'))
			{
				$query = "CREATE TABLE `#__finder_types` (
					  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
					  `title` varchar(100) NOT NULL,
					  `mime` varchar(100) NOT NULL,
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `title` (`title`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
				$db->setQuery($query);
				$db->query();
			}
		}
	}
}