<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for resetting wiki page access value that Joomla auto-set
 **/
class Migration20150105220027ComWiki extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__wiki_page'))
		{
			$query = "UPDATE `#__wiki_page`
					SET `access`=0
					WHERE `access`=1 AND `group_cn` NOT LIKE 'pr-%'";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}