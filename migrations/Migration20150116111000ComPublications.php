<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script to add 'archived' column to #__publication_versions
 **/
class Migration20150116111000ComPublications extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__publication_versions') && !$this->db->tableHasField('#__publication_versions', 'archived'))
		{
			$query = "ALTER TABLE `#__publication_versions` ADD COLUMN `archived` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `accepted`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}