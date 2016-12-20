<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for moving votes from #__vote_log to #__item_votes
 **/
class Migration20161220173600PlgResourcesReviews extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__vote_log') && $this->db->tableExists('#__item_votes'))
		{
			$query = "SELECT * FROM `#__vote_log` WHERE `category`='review'";
			$this->db->setQuery($query);
			$votes = $this->db->loadObjectList();
			foreach ($votes as $vote)
			{
				$query = "INSERT INTO `#__item_votes` (`id`, `item_id`, `item_type`, `ip`, `created`, `created_by`, `vote`)
						VALUES (NULL, " . $this->db->quote($vote->referenceid) . ", " . $this->db->quote($vote->category) . ", " . $this->db->quote($vote->ip) . ", " . $this->db->quote($vote->voted) . ", " . $this->db->quote($vote->voter) . ", " . $this->db->quote($vote->helpful == 'no' ? -1 : 1) . ")";
				$this->db->setQuery($query);
				$this->db->query();
			}

			$query = "DELETE FROM `#__vote_log` WHERE `category`='review'";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__vote_log') && $this->db->tableExists('#__item_votes'))
		{
			$query = "SELECT * FROM `#__item_votes` WHERE `item_type`='review'";
			$this->db->setQuery($query);
			$votes = $this->db->loadObjectList();
			foreach ($votes as $vote)
			{
				$query = "INSERT INTO `#__vote_log` (`id`, `referenceid`, `category`, `ip`, `voted`, `voter`, `helpful`)
						VALUES (NULL, " . $this->db->quote($vote->item_id) . ", " . $this->db->quote($vote->item_type) . ", " . $this->db->quote($vote->ip) . ", " . $this->db->quote($vote->created) . ", " . $this->db->quote($vote->created_by) . ", " . $this->db->quote($vote->helpful == 1 ? 'yes' : 'no') . ")";
				$this->db->setQuery($query);
				$this->db->query();
			}

			$query = "DELETE FROM `#__item_votes` WHERE `item_type`='review'";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}