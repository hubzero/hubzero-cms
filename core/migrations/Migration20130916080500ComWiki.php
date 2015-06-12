<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding params field to asset groups
 **/
class Migration20130916080500ComWiki extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableHasField('#__wiki_page', 'created'))
		{
			$query = "ALTER TABLE `#__wiki_page` ADD `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `created_by`;";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "UPDATE `#__wiki_page` AS p SET p.`created` = (SELECT v.created FROM `#__wiki_version` AS v WHERE v.pageid=p.id ORDER BY v.version ASC LIMIT 1);";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableHasField('#__wiki_page', 'created'))
		{
			$query = "ALTER TABLE `#__wiki_page` DROP `created`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}