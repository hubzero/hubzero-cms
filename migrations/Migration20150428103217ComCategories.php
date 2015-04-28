<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for forcing ROOT record to an ID of "1"
 **/
class Migration20150428103217ComCategories extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__categories'))
		{
			$query = "SELECT * FROM `#__categories` WHERE `alias`='root' AND `level`=0";
			$this->db->setQuery($query);
			$root = $this->db->loadObject();

			if ($root && $root->id != 1)
			{
				// Get the item that has the node's destined ID
				$query = "SELECT * FROM `#__categories` WHERE `id`=1";
				$this->db->setQuery($query);
				$first = $this->db->loadObject();

				if ($first && $first->id)
				{
					// Get the last item in the list
					$query = "SELECT * FROM `#__categories` ORDER BY `id` DESC LIMIT 1";
					$this->db->setQuery($query);
					$last = $this->db->loadObject();

					// Push the first to the last.
					// This shouldn't cause issues as the nested set maintains the
					// proper order. ID should be irrelevant except for ROOT.
					$query = "UPDATE `#__categories` SET `id`=" . ($last->id + 1) . " WHERE `id`=" . $first->id;
					$this->db->setQuery($query);
					$this->db->query();
				}

				// Update the root node's position
				$query = "UPDATE `#__categories` SET `id`=1 WHERE `id`=" . $root->id;
				$this->db->setQuery($query);
				$this->db->query();

				// Update the parent ID on all the node's immediate children
				$query = "UPDATE `#__categories` SET `parent_id`=1 WHERE `parent_id`=" . $root->id;
				$this->db->setQuery($query);
				$this->db->query();
			}

			// Make sure the root node is public
			$query = "UPDATE `#__categories` SET `access`=1 WHERE `id`=1 AND `access`!=1";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}