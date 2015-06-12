<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for stats setup
 **/
class Migration20131111165410Core extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$init_params = array(
			"period"     => "14",
			"chart_path" => "/site/stats/chart_resources/",
			"map_path"   => "/site/stats/resource_maps/"
		);

		if ($this->db->tableExists('#__extensions'))
		{
			$query = 'SELECT `params` FROM `#__extensions` WHERE folder = "resources" AND element = "usage"';
			$this->db->setQuery($query);
			$result = $this->db->loadResult();

			$params = (array) json_decode($result);
		}
		else
		{
			$query = 'SELECT `params` FROM `#__plugins` WHERE folder = "resources" AND element = "usage"';
			$this->db->setQuery($query);
			$result = $this->db->loadResult();

			$params = array();

			if (!empty($result))
			{
				$ar = explode("\n", $result);

				foreach ($ar as $a)
				{
					$a = trim($a);
					if (empty($a))
					{
						continue;
					}

					$ar2     = explode("=", $a, 2);
					$params[$ar2[0]] = (isset($ar2[1])) ? $ar2[1] : '';
				}
			}
		}

		$found = array();

		if (!empty($params) && count($params) > 0)
		{
			foreach ($params as $k => $v)
			{
				if ($k == 'period' && $v == '15')
				{
					$found[] = 'period';
					$params[$k] = '14';
				}
				else if ($k == 'chart_path' && $v == '/site/usage/chart_resources/')
				{
					$found[] = 'chart_path';
					$params[$k] = '/site/stats/chart_resources/';
				}
				else if ($k == 'map_path' && $v == '/site/usage/resource_maps/')
				{
					$found[] = 'map_path';
					$params[$k] = '/site/stats/resource_maps/';
				}
			}

			if (!in_array('period', $found))
			{
				$params['period'] = '14';
			}
			if (!in_array('chart_path', $found))
			{
				$params['chart_path'] = '/site/stats/chart_resources/';
			}
			if (!in_array('map_path', $found))
			{
				$params['map_path'] = '/site/stats/resource_maps/';
			}
		}
		else
		{
			$params = $init_params;
		}

		if ($this->db->tableExists('#__extensions'))
		{
			$params = json_encode($params);

			$query = 'UPDATE `#__extensions` SET params = '.$this->db->quote($params).' WHERE folder = "resources" AND element = "usage"';
			$this->db->setQuery($query);
			$this->db->query();
		}
		else
		{
			$p = '';
			foreach ($params as $k => $v)
			{
				$p .= "{$k}={$v}\n";
			}

			$params = $p;

			$query = 'UPDATE `#__plugins` SET params = '.$this->db->quote($params).' WHERE folder = "resources" AND element = "usage"';
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__resource_stats_tools_tops'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__resource_stats_tools_tops` (
						`top` tinyint(4) NOT NULL default '0',
						`name` varchar(128) NOT NULL default '',
						`valfmt` tinyint(4) NOT NULL default '0',
						`size` tinyint(4) NOT NULL default '0',
						PRIMARY KEY  (`top`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
			$this->db->setQuery($query);
			$this->db->query();

			$query  = "INSERT IGNORE INTO `#__resource_stats_tools_tops` VALUES";
			$query .= " (1,'Users By Country Of Residence',1,5),";
			$query .= " (2,'Top Domains By User Count',1,5),";
			$query .= " (3,'Users By Organization Type',1,5)";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__stats_tops'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__stats_tops` (
						`id` tinyint(4) NOT NULL default '0',
						`name` varchar(128) NOT NULL default '',
						`valfmt` tinyint(4) NOT NULL default '0',
						`size` tinyint(4) NOT NULL default '0',
						PRIMARY KEY  (`id`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
			$this->db->setQuery($query);
			$this->db->query();

			$query  = "INSERT IGNORE INTO `#__stats_tops` VALUES";
			$query .= " (1,'Top Tools by Ranking',1,5),";
			$query .= " (2,'Top Tools by Simulation Users',1,5),";
			$query .= " (3,'Top Tools by Interactive Sessions',1,5),";
			$query .= " (4,'Top Tools by Simulation Sessions',1,5),";
			$query .= " (5,'Top Tools by Simulation Runs',1,5),";
			$query .= " (6,'Top Tools by Simulation Wall Time',2,5),";
			$query .= " (7,'Top Tools by Simulation CPU Time',2,5),";
			$query .= " (8,'Top Tools by Simulation Interaction Time',2,5),";
			$query .= " (9,'Top Tools by Citations',1,5)";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__citations_secondary'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__citations_secondary` (
						`id` int(11) NOT NULL auto_increment,
						`cid` int(11) NOT NULL,
						`sec_cits_cnt` int(11) default NULL,
						`search_string` tinytext,
						PRIMARY KEY  (`id`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__session_geo'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__session_geo` (
						`session_id` varchar(200) NOT NULL default '0',
						`username` varchar(150) default '',
						`time` varchar(14) default '',
						`guest` tinyint(4) default '1',
						`userid` int(11) default '0',
						`ip` varchar(15) default NULL,
						`host` varchar(128) default NULL,
						`domain` varchar(128) default NULL,
						`signed` tinyint(3) default '0',
						`countrySHORT` char(2) default NULL,
						`countryLONG` varchar(64) default NULL,
						`ipREGION` varchar(128) default NULL,
						`ipCITY` varchar(128) default NULL,
						`ipLATITUDE` double default NULL,
						`ipLONGITUDE` double default NULL,
						`bot` tinyint(4) default '0',
						PRIMARY KEY  (`session_id`),
						KEY `userid` (`userid`),
						KEY `time` (`time`),
						KEY `ip` (`ip`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__metrics_ipgeo_cache'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__metrics_ipgeo_cache` (
						`ip` int(10) NOT NULL DEFAULT '0000000000',
						`countrySHORT` char(2) NOT NULL DEFAULT '',
						`countryLONG` varchar(64) NOT NULL DEFAULT '',
						`ipREGION` varchar(128) NOT NULL DEFAULT '',
						`ipCITY` varchar(128) NOT NULL DEFAULT '',
						`ipLATITUDE` double DEFAULT NULL,
						`ipLONGITUDE` double DEFAULT NULL,
						`lookup_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
						PRIMARY KEY (`ip`),
						KEY (`lookup_datetime`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}