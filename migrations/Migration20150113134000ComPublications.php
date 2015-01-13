<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script to remove 'ark' column from #__publication_versions
 **/
class Migration20150113134000ComPublications extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__publication_versions') && $this->db->tableHasField('#__publication_versions', 'ark'))
		{
			$query = "ALTER TABLE `#__publication_versions` DROP COLUMN `ark`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}