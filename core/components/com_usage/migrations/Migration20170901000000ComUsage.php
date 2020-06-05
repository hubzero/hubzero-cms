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
 * Migration script for installing needed tables
 **/
class Migration20170901000000ComUsage extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		try
		{
			if (!file_exists(dirname(__DIR__) . '/helpers/helper.php'))
			{
				$this->log('Unable to locate usage helper class.', 'error');
				return;
			}

			include_once dirname(__DIR__) . '/helpers/helper.php';

			$db = \Components\Usage\Helpers\Helper::getUDBO();

			if (!$db)
			{
				$this->log('Unable to establish connection for usage database.', 'error');
				return;
			}

			if (!$db->tableExists('tops'))
			{
				$query = "CREATE TABLE `tops` (
				  `top` tinyint(4) NOT NULL DEFAULT '0',
				  `name` varchar(128) NOT NULL DEFAULT '',
				  `valfmt` tinyint(4) NOT NULL DEFAULT '0',
				  `size` tinyint(4) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`top`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

				$db->setQuery($query);
				$db->query();

				$this->log("Created usage table `tops`");
			}

			if (!$db->tableExists('topvals'))
			{
				$query = "CREATE TABLE `topvals` (
				  `top` tinyint(4) NOT NULL DEFAULT '0',
				  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				  `period` tinyint(4) NOT NULL DEFAULT '1',
				  `rank` tinyint(4) NOT NULL DEFAULT '0',
				  `name` varchar(255) DEFAULT NULL,
				  `value` bigint(20) NOT NULL DEFAULT '0',
				  KEY `top` (`top`),
				  KEY `top_2` (`top`,`rank`),
				  KEY `top_3` (`top`,`datetime`),
				  KEY `top_4` (`top`,`datetime`,`rank`),
				  KEY `top_5` (`top`,`datetime`,`period`),
				  KEY `hub_2` (`top`),
				  KEY `hub_3` (`top`,`datetime`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

				$db->setQuery($query);
				$db->query();

				$this->log("Created usage table `topvals`");
			}

			if (!$db->tableExists('regions'))
			{
				$query = "CREATE TABLE `regions` (
				  `region` tinyint(4) NOT NULL DEFAULT '0',
				  `name` varchar(128) NOT NULL DEFAULT '',
				  `valfmt` tinyint(4) NOT NULL DEFAULT '0',
				  `size` tinyint(4) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`region`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

				$db->setQuery($query);
				$db->query();

				$this->log("Created usage table `regions`");
			}

			if (!$db->tableExists('regionvals'))
			{
				$query = "CREATE TABLE `regionvals` (
				  `region` tinyint(4) NOT NULL DEFAULT '0',
				  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				  `period` tinyint(4) NOT NULL DEFAULT '0',
				  `rank` tinyint(4) NOT NULL DEFAULT '0',
				  `name` varchar(255) DEFAULT NULL,
				  `value` bigint(20) NOT NULL DEFAULT '0'
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

				$db->setQuery($query);
				$db->query();

				$this->log("Created usage table `regionvals`");
			}

			if (!$db->tableExists('totalvals'))
			{
				$query = "CREATE TABLE `totalvals` (
				  `hub` tinyint(4) NOT NULL DEFAULT '0',
				  `total` tinyint(4) NOT NULL DEFAULT '0',
				  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				  `period` tinyint(4) NOT NULL DEFAULT '1',
				  `value` bigint(20) NOT NULL DEFAULT '0',
				  KEY `hub` (`hub`),
				  KEY `total` (`total`),
				  KEY `hub_2` (`hub`,`total`),
				  KEY `hub_3` (`hub`,`total`,`datetime`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

				$db->setQuery($query);
				$db->query();

				$this->log("Created usage table `totalvals`");
			}

			if (!$db->tableExists('classes'))
			{
				$query = "CREATE TABLE `classes` (
				  `class` tinyint(4) NOT NULL DEFAULT '0',
				  `name` varchar(128) NOT NULL DEFAULT '',
				  `valfmt` tinyint(4) NOT NULL DEFAULT '0',
				  `size` tinyint(4) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`class`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

				$db->setQuery($query);
				$db->query();

				$this->log("Created usage table `classes`");
			}

			if (!$db->tableExists('classvals'))
			{
				$query = "CREATE TABLE `classvals` (
				  `class` tinyint(4) NOT NULL DEFAULT '0',
				  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				  `period` tinyint(4) NOT NULL DEFAULT '0',
				  `rank` tinyint(4) NOT NULL DEFAULT '0',
				  `name` varchar(255) DEFAULT NULL,
				  `value` bigint(20) NOT NULL DEFAULT '0',
				  KEY `hub_2` (`class`),
				  KEY `class` (`class`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

				$db->setQuery($query);
				$db->query();

				$this->log("Created usage table `classvals`");
			}
		}
		catch (\Exception $e)
		{
			$this->log($e->getMessage(), 'error');
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		try
		{
			if (!file_exists(dirname(__DIR__) . '/helpers/helper.php'))
			{
				$this->log('Unable to locate usage helper class.', 'error');
				return;
			}

			include_once dirname(__DIR__) . '/helpers/helper.php';

			$db = \Components\Usage\Helpers\Helper::getUDBO();

			if (!$db)
			{
				$this->log('Unable to establish connection for usage database.', 'error');
				return;
			}

			$tables = array(
				'tops',
				'topvals',
				'regions',
				'regionvals',
				'classes',
				'classvals',
				'totalvals'
			);

			foreach ($tables as $table)
			{
				if ($db->tableExists($table))
				{
					$query = "DROP TABLE IF EXISTS `$table`;";
					$db->setQuery($query);
					$db->query();

					$this->log("Dropped usage table `$table`");
				}
			}
		}
		catch (\Exception $e)
		{
			$this->log($e->getMessage(), 'error');
		}
	}
}
