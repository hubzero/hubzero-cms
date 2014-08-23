<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding potentially missing alias field on polls table
 **/
class Migration20140822203559ComPoll extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__polls') && !$this->db->tableHasField('#__polls', 'alias'))
		{
			$query = "ALTER TABLE `#__polls` ADD `alias` VARCHAR(255) NOT NULL DEFAULT '' AFTER `title`";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "SELECT `id`, `title` FROM `#__polls`";
			$this->db->setQuery($query);
			$polls = $this->db->loadObjectList();

			if ($polls && count($polls) > 0)
			{
				foreach ($polls as $poll)
				{
					$alias = preg_replace("/[^a-zA-Z0-9]/", '', $poll->title);
					$alias = strtolower($alias);
					$query = "UPDATE `#__polls` SET `alias` = ". $this->db->quote($alias) . " WHERE `id` = '{$poll->id}'";
					$this->db->setQuery($query);
					$this->db->query();
				}
			}
		}
	}
}