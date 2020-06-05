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
 * Migration script to create the `#__solr_filters` and `#__solr_search_filter_options` tables
 **/
class Migration20180724112946ComSearch extends Base
{
	static $filtersTable = '#__solr_search_filters';
	static $filterOptionsTable = '#__solr_search_filter_options';

	/**
	 * Up
	 **/
	public function up()
	{
		$filtersTableName = self::$filtersTable;
		$filterOptionsTableName = self::$filterOptionsTable;
		if (!$this->db->tableExists($filtersTableName))
		{
			$filterTable = "CREATE TABLE `{$filtersTableName}` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`component_id` int(11) unsigned NOT NULL,
				`type` varchar(255) NOT NULL,
				`field` varchar(255) NOT NULL DEFAULT '',
				`label` varchar(255) NOT NULL DEFAULT '',
				`params` varchar(255) DEFAULT NULL,
				`ordering` int(11) NOT NULL DEFAULT '0',
				`created` datetime DEFAULT NULL,
				`created_by` int(11) NOT NULL DEFAULT '0',
				`modified` datetime DEFAULT NULL,
				`modified_by` int(11) NOT NULL DEFAULT '0',
				PRIMARY KEY (`id`),
				KEY `idx_type` (`type`),
				KEY `idx_field` (`field`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
			$this->db->setQuery($filterTable);
			$this->db->query();
		}

		if (!$this->db->tableExists($filterOptionsTableName))
		{
			$filterOptionsTable = "CREATE TABLE `{$filterOptionsTableName}` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`filter_id` int(11) NOT NULL DEFAULT '0',
				`value` varchar(255) NOT NULL DEFAULT '',
				`label` varchar(255) NOT NULL DEFAULT '',
				`ordering` int(11) NOT NULL DEFAULT '0',
				`created` datetime DEFAULT NULL,
				`created_by` int(11) NOT NULL DEFAULT '0',
				`modified` datetime DEFAULT NULL,
				`modified_by` int(11) NOT NULL DEFAULT '0',
				PRIMARY KEY (`id`),
				KEY `idx_filter_id` (`filter_id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
			$this->db->setQuery($filterOptionsTable);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function	down()
	{
		$filtersTableName = self::$filtersTable;
		$filterOptionsTableName = self::$filterOptionsTable;

		if ($this->db->tableExists($filtersTableName))
		{
			$dropTable = "DROP TABLE {$filtersTableName};";
			$this->db->setQuery($dropTable);
			$this->db->query();
		}

		if ($this->db->tableExists($filterOptionsTableName))
		{
			$dropTable = "DROP TABLE {$filtersTableName};";
			$this->db->setQuery($dropTable);
			$this->db->query();
		}
	}
}
