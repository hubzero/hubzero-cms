<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding 'unique' and 'unfiltered' fields to publication logs table
 **/
class Migration20141119164113ComPublications extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__publication_logs'))
		{
			if (!$this->db->tableHasField('#__publication_logs', 'page_views_unfiltered'))
			{
				$query = "ALTER TABLE `#__publication_logs` ADD COLUMN page_views_unfiltered int(11);";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if (!$this->db->tableHasField('#__publication_logs', 'primary_accesses_unfiltered'))
			{
				$query = "ALTER TABLE `#__publication_logs` ADD COLUMN primary_accesses_unfiltered int(11);";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if (!$this->db->tableHasField('#__publication_logs', 'page_views_unique'))
			{
				$query = "ALTER TABLE `#__publication_logs` ADD COLUMN page_views_unique int(11);";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if (!$this->db->tableHasField('#__publication_logs', 'primary_accesses_unique'))
			{
				$query = "ALTER TABLE `#__publication_logs` ADD COLUMN primary_accesses_unique int(11);";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}