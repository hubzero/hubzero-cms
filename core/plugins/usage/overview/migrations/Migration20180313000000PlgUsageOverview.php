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
 * Migration script for adding Usage overview tables
 **/
class Migration20180313000000PlgUsageOverview extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('summary_user'))
		{
			$query = "CREATE TABLE `summary_user` (
			  `id` tinyint(4) NOT NULL DEFAULT '0',
			  `label` varchar(255) NOT NULL DEFAULT '',
			  `plot` int(1) DEFAULT '0',
			  UNIQUE KEY `label` (`label`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('summary_user_vals'))
		{
			$query = "CREATE TABLE `summary_user_vals` (
			  `rowid` tinyint(4) NOT NULL DEFAULT '0',
			  `colid` tinyint(4) NOT NULL DEFAULT '0',
			  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			  `period` tinyint(4) NOT NULL DEFAULT '1',
			  `value` bigint(20) DEFAULT '0',
			  `valfmt` tinyint(4) NOT NULL DEFAULT '0'
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('summary_simusage_vals'))
		{
			$query = "CREATE TABLE `summary_simusage_vals` (
			  `rowid` tinyint(4) NOT NULL DEFAULT '0',
			  `colid` tinyint(4) NOT NULL DEFAULT '0',
			  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			  `period` tinyint(4) NOT NULL DEFAULT '1',
			  `value` bigint(20) DEFAULT '0',
			  `valfmt` tinyint(4) NOT NULL DEFAULT '0'
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('summary_simusage'))
		{
			$query = "CREATE TABLE `summary_simusage` (
			  `id` tinyint(4) NOT NULL DEFAULT '0',
			  `label` varchar(255) NOT NULL DEFAULT '',
			  `plot` int(1) DEFAULT '0',
			  UNIQUE KEY `label` (`label`)
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
		if ($this->db->tableExists('summary_user'))
		{
			$query = "DROP TABLE IF EXISTS `summary_user`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('summary_user_vals'))
		{
			$query = "DROP TABLE IF EXISTS `summary_user_vals`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('summary_simusage_vals'))
		{
			$query = "DROP TABLE IF EXISTS `summary_simusage_vals`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('summary_simusage'))
		{
			$query = "DROP TABLE IF EXISTS `summary_simusage`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
