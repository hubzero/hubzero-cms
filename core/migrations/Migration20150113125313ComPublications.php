<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding handler association table
 **/
class Migration20150113125313ComPublications extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__publication_handler_assoc'))
		{
			$query = "CREATE TABLE `jos_publication_handler_assoc` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `publication_version_id` int(11) NOT NULL,
			  `element_id` int(11) NOT NULL,
			  `handler_id` int(11) NOT NULL,
			  `params` text NOT NULL,
			  `ordering` int(11) NOT NULL DEFAULT '1',
			  `status` tinyint(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	public function down()
	{
		if ($this->db->tableExists('#__publication_handler_assoc'))
		{
			$query = "DROP TABLE IF EXISTS `#__publication_handler_assoc`;\n";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}