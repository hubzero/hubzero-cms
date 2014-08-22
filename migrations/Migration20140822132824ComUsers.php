<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for putting group-less members into the default group
 **/
class Migration20140822132824ComUsers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		// Get all users that have no group set
		$query  = "SELECT `id` FROM `#__users` AS u";
		$query .= " LEFT JOIN `#__user_usergroup_map` AS um ON u.id = um.user_id";
		$query .= " WHERE `group_id` IS NULL";

		$this->db->setQuery($query);
		$ids = $this->db->loadColumn();

		if ($ids && count($ids) > 0)
		{
			// Get the default new user group
			$group_id = $this->getParams('com_users')->get('new_usertype');

			if (!isset($group_id) || !is_numeric($group_id))
			{
				$this->setError('Failed to retrieve a proper new user type. Please ensure one has been set.', 'warning');
				return;
			}

			$group_id = $this->db->quote($group_id);

			foreach ($ids as $id)
			{
				$id = $this->db->quote($id);
				$query = "INSERT INTO `#__user_usergroup_map` (`user_id`, `group_id`) VALUES ({$id}, {$group_id})";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}