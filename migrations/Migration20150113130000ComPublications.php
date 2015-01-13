<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script to add 'curator' column to #__publication_versions
 **/
class Migration20150113130000ComPublications extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__publication_versions'))
		{
			if (!$this->db->tableHasField('#__publication_versions', 'curator'))
			{
				$query = "ALTER TABLE `#__publication_versions` ADD COLUMN curator int(11);";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}