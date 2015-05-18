<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding state column to publication ratings table
 **/
class Migration20150518190000ComPublications extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__publication_ratings'))
		{
			if (!$this->db->tableHasField('#__publication_ratings', 'state'))
			{
				$query = "ALTER TABLE `#__publication_ratings` ADD COLUMN state tinyint(2) NOT NULL DEFAULT '1'";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}