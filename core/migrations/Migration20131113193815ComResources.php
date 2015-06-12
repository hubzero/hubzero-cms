<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for replacing odd characters in resource license text
 **/
class Migration20131113193815ComResources extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__resource_licenses'))
		{
			$query = "UPDATE `#__resource_licenses` SET `text` = REPLACE(`text`, 'â€”', '—')";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}