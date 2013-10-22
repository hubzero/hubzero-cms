<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for moving member manager notes to joomla notes feature
 **/
class Migration20131022144858ComMembers extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		if ($db->tableExists('#__xprofiles_manager') && $db->tableExists('#__user_notes'))
		{
			// Get admin user id number (probabaly 62)
			$query = "SELECT `id` FROM `#__users` WHERE username = 'admin'";
			$db->setQuery($query);
			$admin_id = (int) $db->loadResult();

			// Start by grabbing all xprofile_manager entries
			$query = "SELECT * FROM `#__xprofiles_manager`";
			$db->setQuery($query);
			$results = $db->loadObjectList();

			if ($results && count($results) > 0)
			{
				foreach ($results as $r)
				{
					$query = "INSERT INTO `#__user_notes` (`user_id`, `subject`, `state`, `created_user_id`, `created_time`) VALUES ";
					$query .= "('{$r->uidNumber}', ".$db->quote($r->manager).", '1', '{$admin_id}', '".date("Y-m-d H:i:s")."')";
					$db->setQuery($query);
					$db->query();
				}
			}

			$query = "DROP TABLE `#__xprofiles_manager`;";
			$db->setQuery($query);
			$db->query();
		}
	}
}