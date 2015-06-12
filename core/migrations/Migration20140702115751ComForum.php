<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for setting state=3 on reported forum posts
 **/
class Migration20140702115751ComForum extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableHasField('#__forum_posts', 'state'))
		{
			$query = "SELECT referenceid FROM `#__abuse_reports` WHERE state=0 AND category IN ('forum')";
			$this->db->setQuery($query);
			if ($ids = $this->db->loadResultArray())
			{
				$ids = array_map('intval', $ids);

				$query = "UPDATE `#__forum_posts` SET state=3 WHERE id IN (" . implode(',', $ids) . ")";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableHasField('#__forum_posts', 'state'))
		{
			$query = "UPDATE `#__forum_posts` SET state=1 WHERE state=3";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}