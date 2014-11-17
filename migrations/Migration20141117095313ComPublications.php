<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding comment field to curation history table
 **/
class Migration20141117095313ComPublications extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__publication_curation_history'))
		{
			if (!$this->db->tableHasField('#__publication_curation_history', 'comment'))
			{
				$query = "ALTER TABLE `#__publication_curation_history` ADD COLUMN comment TEXT AFTER newstatus;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}