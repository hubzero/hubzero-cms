<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding master_doi field to #__publications
 **/
class Migration20150305100000ComPublications extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__publications'))
		{
			if (!$this->db->tableHasField('#__publications', 'master_doi'))
			{
				$query = "ALTER TABLE `#__publications` ADD COLUMN master_doi varchar(255) DEFAULT '';";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}