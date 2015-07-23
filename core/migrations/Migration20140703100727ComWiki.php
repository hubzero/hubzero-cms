<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for setting status=3 on wiki comments
 **/
class Migration20140703100727ComWiki extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableHasField('#__wiki_comments', 'status'))
		{
			// Old flagged state was 1. Change it to 3.
			$query = "UPDATE `#__wiki_comments` SET status=3 WHERE status=1";
			$this->db->setQuery($query);
			$this->db->query();

			// Mark all published entries as 1
			$query = "UPDATE `#__wiki_comments` SET status=1 WHERE status=0";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "SELECT referenceid FROM `#__abuse_reports` WHERE state=0 AND category IN ('wiki', 'wikicomment')";
			$this->db->setQuery($query);
			if ($ids = $this->db->loadColumn())
			{
				$ids = array_map('intval', $ids);

				$query = "UPDATE `#__wiki_comments` SET status=3 WHERE id IN (" . implode(',', $ids) . ")";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		$this->addPluginEntry('support', 'wiki');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableHasField('#__wiki_comments', 'status'))
		{
			$query = "UPDATE `#__wiki_comments` SET status=0 WHERE status=1";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "UPDATE `#__wiki_comments` SET status=1 WHERE status=3";
			$this->db->setQuery($query);
			$this->db->query();
		}

		$this->deletePluginEntry('support', 'wiki');
	}
}