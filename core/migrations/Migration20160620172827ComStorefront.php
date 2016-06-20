<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script to add a table for product images and a couple extra fields
 **/
class Migration20160620172827ComStorefront extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__storefront_images'))
		{
			$query = "CREATE TABLE `#__storefront_images` (
			  `imgId` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `imgName` char(255) DEFAULT NULL,
			  `imgObject` char(25) DEFAULT NULL,
			  `imgObjectId` int(11) DEFAULT NULL,
			  `imgPrimary` tinyint(1) DEFAULT '1',
			  PRIMARY KEY (`imgId`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__storefront_option_groups') && !$this->db->tableHasField('#__storefront_option_groups', 'ogActive'))
		{
			$query = "ALTER TABLE `#__storefront_option_groups` ADD `ogActive` tinyint(1) NOT NULL DEFAULT '0'";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__storefront_options') && !$this->db->tableHasField('#__storefront_options', 'oActive'))
		{
			$query = "ALTER TABLE `#__storefront_options` ADD `oActive` tinyint(1) NOT NULL DEFAULT '0'";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__storefront_collections') && !$this->db->tableHasField('#__storefront_collections', 'cAlias'))
		{
			$query = "ALTER TABLE `#__storefront_collections` ADD `cAlias` char(50)";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__storefront_images'))
		{
			$query = "DROP TABLE `#__storefront_images`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__storefront_option_groups') && $this->db->tableHasField('#__storefront_option_groups', 'ogActive'))
		{
			$query = "ALTER TABLE `#__storefront_option_groups` DROP COLUMN `ogActive`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__storefront_options') && $this->db->tableHasField('#__storefront_options', 'oActive'))
		{
			$query = "ALTER TABLE `#__storefront_options` DROP COLUMN `oActive`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__storefront_collections') && $this->db->tableHasField('#__storefront_collections', 'cAlias'))
		{
			$query = "ALTER TABLE `#__storefront_collections` DROP COLUMN `cAlias`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
