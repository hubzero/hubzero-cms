<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for setting up publication building blocks
 **/
class Migration20140512120000ComPublications extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$queries = array();

		// Set up curation
		if (!$this->db->tableExists('#__publication_curaton'))
		{
			$queries[] = "CREATE TABLE IF NOT EXISTS `#__publication_curation` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`publication_id` int(11) NOT NULL DEFAULT '0',
				`publication_version_id` int(11) NOT NULL DEFAULT '0',
				`updated` datetime DEFAULT NULL,
				`updated_by` int(11) DEFAULT '0',
				`update` text,
				`reviewed` datetime DEFAULT NULL,
				`reviewed_by` int(11) DEFAULT '0',
				`review` text,
				`review_status` int(11) NOT NULL DEFAULT '0',
				`block` varchar(100) NOT NULL DEFAULT '',
				`step` int(11) DEFAULT '0',
				`element` int(11) DEFAULT '0',
				PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		}

		// Set up curation blocks
		if (!$this->db->tableExists('#__publication_blocks'))
		{
			$queries[] = "CREATE TABLE IF NOT EXISTS `#__publication_blocks` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`block` varchar(100) NOT NULL DEFAULT '',
				`label` varchar(100) NOT NULL DEFAULT '',
				`title` varchar(255) NOT NULL DEFAULT '',
				`status` int(11) NOT NULL DEFAULT '0',
				`minimum` int(11) NOT NULL DEFAULT '0',
				`maximum` int(11) NOT NULL DEFAULT '0',
				`ordering` int(11) NOT NULL DEFAULT '0',
				`params` text,
				`manifest` text,
				PRIMARY KEY (`id`),
				UNIQUE KEY `block` (`block`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8";

			// Set default blocks
			$queries[] = "INSERT INTO `#__publication_blocks` (`id`,`block`, `label`, `title`, `status`,
						 `minimum`, `maximum`, `ordering`, `params`, `manifest`)
						  VALUES ('1','content','Content', 'Publication Content', '1', '1', '5', '1', '', '')";

			$queries[] = "INSERT INTO `#__publication_blocks` (`id`,`block`, `label`, `title`, `status`,
						 `minimum`, `maximum`, `ordering`, `params`, `manifest`)
						  VALUES ('2','description','Description', 'Publication Description', '1', '1', '5', '2', '', '')";

			$queries[] = "INSERT INTO `#__publication_blocks` (`id`,`block`, `label`, `title`, `status`,
						 `minimum`, `maximum`, `ordering`, `params`, `manifest`)
						  VALUES ('3','authors','Authors', 'Publication Authors', '1', '1', '1', '3', '', '')";

			$queries[] = "INSERT INTO `#__publication_blocks` (`id`,`block`, `label`, `title`, `status`,
						 `minimum`, `maximum`, `ordering`, `params`, `manifest`)
						  VALUES ('4','extras','Extras', 'Publication Extra Content', '1', '0', '1', '4', 'default=1', '')";

			$queries[] = "INSERT INTO `#__publication_blocks` (`id`,`block`, `label`, `title`, `status`,
						 `minimum`, `maximum`, `ordering`, `params`, `manifest`)
						  VALUES ('5','license','License', 'Publication Tags', '1', '0', '1', '5', 'default=1', '')";

			$queries[] = "INSERT INTO `#__publication_blocks` (`id`,`block`, `label`, `title`, `status`,
						 `minimum`, `maximum`, `ordering`, `params`, `manifest`)
						  VALUES ('6','tags','Tags', 'Publication Tags', '1', '0', '1', '6', 'default=1', '')";

			$queries[] = "INSERT INTO `#__publication_blocks` (`id`,`block`, `label`, `title`, `status`,
						 `minimum`, `maximum`, `ordering`, `params`, `manifest`)
						  VALUES ('7','notes','Notes', 'Version Release Notes', '1', '0', '1', '7', 'default=1', '')";

			$queries[] = "INSERT INTO `#__publication_blocks` (`id`,`block`, `label`, `title`, `status`,
						 `minimum`, `maximum`, `ordering`, `params`, `manifest`)
						  VALUES ('8','review','Review', 'Publication Review', '1', '1', '1', '8', 'default=1', '')";
		}

		// Set up handlers
		if (!$this->db->tableExists('#__publication_handlers'))
		{
			$queries[] = "CREATE TABLE IF NOT EXISTS `#__publication_handlers` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`name` varchar(100) NOT NULL DEFAULT '',
				`label` varchar(100) NOT NULL DEFAULT '',
				`title` varchar(255) NOT NULL DEFAULT '',
				`status` int(11) NOT NULL DEFAULT '0',
				`about` text,
				`params` text,
				PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8";

			$queries[] = "INSERT INTO `#__publication_handlers` (`id`,`name`, `label`, `title`, `status`, `about`, `params`)
						  VALUES ('1','imageviewer','Image Viewer', 'Image Gallery Presenter', '1', '', '')";

			// Add curation field
			if (!$this->db->tableHasField('#__publication_versions', 'curation'))
			{
				$queries[] = "ALTER TABLE `#__publication_versions` ADD `curation` TEXT  NULL;";
			}
			// Add reviewed field
			if (!$this->db->tableHasField('#__publication_versions', 'reviewed'))
			{
				$queries[] = "ALTER TABLE `#__publication_versions` ADD `reviewed` datetime NULL;";
			}
			// Add reviewed_by field
			if (!$this->db->tableHasField('#__publication_versions', 'reviewed_by'))
			{
				$queries[] = "ALTER TABLE `#__publication_versions` ADD `reviewed_by` int(11);";
			}
			// Add curation field
			if (!$this->db->tableHasField('#__publication_master_types', 'curation'))
			{
				$queries[] = "ALTER TABLE `#__publication_master_types` ADD `curation` TEXT  NULL;";
			}
			// Add curation group field
			if (!$this->db->tableHasField('#__publication_master_types', 'curatorgroup'))
			{
				$queries[] = "ALTER TABLE `#__publication_master_types` ADD `curatorgroup` int(11);";
			}
			// Add element field
			if (!$this->db->tableHasField('#__publication_attachments', 'element_id'))
			{
				$queries[] = "ALTER TABLE `#__publication_attachments` ADD `element_id` int(11) NOT NULL DEFAULT '0';";
			}
		}

		// Run queries
		if (count($queries) > 0)
		{
			// Run queries
			foreach ($queries as $query)
			{
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$queries = array();

		if ($this->db->tableExists('#__publication_blocks'))
		{
			$queries[] = "DROP TABLE IF EXISTS `#__publication_blocks`";
		}
		if ($this->db->tableExists('#__publication_curation'))
		{
			$queries[] = "DROP TABLE IF EXISTS `#__publication_curation`";
		}
		if ($this->db->tableExists('#__publication_handlers'))
		{
			$queries[] = "DROP TABLE IF EXISTS `#__publication_handlers`";
		}

		if (count($queries) > 0)
		{
			// Run queries
			foreach ($queries as $query)
			{
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}