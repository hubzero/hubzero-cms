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
 * Migration script for removing component entry for com_store
 **/
class Migration20171218000000ComStore extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->deleteComponentEntry('store');

		if ($this->db->tableExists('#__store'))
		{
			$query = "DROP TABLE IF EXISTS `#__store`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__orders'))
		{
			$query = "DROP TABLE IF EXISTS `#__orders`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__order_items'))
		{
			$query = "DROP TABLE IF EXISTS `#__order_items`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__cart'))
		{
			$query = "DROP TABLE IF EXISTS `#__cart`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		$path = PATH_APP . '/site/store';

		if (is_dir($path))
		{
			$this->rrmdir($path);
		}
	}

	/**
	 * Recursively remove a directory
	 *
	 * @param   string  $src
	 * @return  void
	 **/
	private function rrmdir($src)
	{
		$dir = opendir($src);
		while (false !== ($file = readdir($dir)))
		{
			if ($file != '.' && $file != '..')
			{
				$full = $src . '/' . $file;
				if (is_dir($full))
				{
					$this->rrmdir($full);
				}
				else
				{
					@unlink($full);
				}
			}
		}
		closedir($dir);
		@rmdir($src);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->addComponentEntry('store');

		if (!$this->db->tableExists('#__store'))
		{
			$query = "CREATE TABLE `#__store` (
			  `id` int(10) NOT NULL AUTO_INCREMENT,
			  `title` varchar(127) NOT NULL DEFAULT '',
			  `price` int(11) NOT NULL DEFAULT '0',
			  `description` text,
			  `published` tinyint(1) NOT NULL DEFAULT '0',
			  `featured` tinyint(1) NOT NULL DEFAULT '0',
			  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			  `available` int(1) NOT NULL DEFAULT '0',
			  `params` text,
			  `special` int(11) DEFAULT '0',
			  `type` int(11) DEFAULT '1',
			  `category` varchar(127) DEFAULT '',
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__orders'))
		{
			$query = "CREATE TABLE `#__orders` (
			  `id` int(10) NOT NULL AUTO_INCREMENT,
			  `uid` int(11) NOT NULL DEFAULT '0',
			  `type` varchar(20) DEFAULT NULL,
			  `total` int(11) DEFAULT '0',
			  `status` int(11) NOT NULL DEFAULT '0',
			  `details` text,
			  `email` varchar(150) DEFAULT NULL,
			  `ordered` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			  `status_changed` datetime DEFAULT '0000-00-00 00:00:00',
			  `notes` text,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__order_items'))
		{
			$query = "CREATE TABLE `#__order_items` (
			  `id` int(10) NOT NULL AUTO_INCREMENT,
			  `oid` int(11) NOT NULL DEFAULT '0',
			  `uid` int(11) NOT NULL DEFAULT '0',
			  `itemid` int(11) NOT NULL DEFAULT '0',
			  `price` int(11) NOT NULL DEFAULT '0',
			  `quantity` int(11) NOT NULL DEFAULT '0',
			  `selections` text,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__cart'))
		{
			$query = "CREATE TABLE `#__cart` (
			  `id` int(10) NOT NULL AUTO_INCREMENT,
			  `uid` int(11) NOT NULL DEFAULT '0',
			  `itemid` int(11) NOT NULL DEFAULT '0',
			  `type` varchar(20) DEFAULT NULL,
			  `quantity` int(11) NOT NULL DEFAULT '0',
			  `added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			  `selections` text,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
