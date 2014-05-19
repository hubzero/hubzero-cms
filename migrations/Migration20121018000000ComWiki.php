<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding modified, version_id, and length fields to wiki table
 **/
class Migration20121018000000ComWiki extends Base
{
	public function up()
	{
		$query = '';

		if (!$this->db->tableHasField('#__wiki_page', 'modified'))
		{
			$query .= "ALTER TABLE `#__wiki_page` ADD `modified` DATETIME  NOT NULL  DEFAULT '0000-00-00 00:00:00'  AFTER `state`;\n";
		}
		if (!$this->db->tableHasField('#__wiki_page', 'version_id'))
		{
			$query .= "ALTER TABLE `#__wiki_page` ADD `version_id` INT(11)  NOT NULL  DEFAULT '0'  AFTER `modified`;\n";
		}
		if (!$this->db->tableHasField('#__wiki_version', 'length'))
		{
			$query .= "ALTER TABLE `#__wiki_version` ADD `length` INT(11)  NOT NULL  DEFAULT '0'  AFTER `summary`;\n";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}