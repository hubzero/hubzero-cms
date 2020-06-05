<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;
use Hubzero\Utility\Date;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for moving member manager notes to user notes table
 **/
class Migration20131022144858ComMembers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__xprofiles_manager') && $this->db->tableExists('#__user_notes'))
		{
			// Get admin user id number (probabaly 62)
			$query = "SELECT `id` FROM `#__users` WHERE username = 'admin'";
			$this->db->setQuery($query);
			$admin_id = (int) $this->db->loadResult();

			// Start by grabbing all xprofile_manager entries
			$query = "SELECT * FROM `#__xprofiles_manager`";
			$this->db->setQuery($query);
			$results = $this->db->loadObjectList();

			if ($results && count($results) > 0)
			{
				foreach ($results as $r)
				{
					$query = "INSERT INTO `#__user_notes` (`user_id`, `subject`, `state`, `created_user_id`, `created_time`) VALUES ";
					$query .= "('{$r->uidNumber}', ".$this->db->quote($r->manager).", '1', '{$admin_id}', '".with(new Date)->toSql()."')";
					$this->db->setQuery($query);
					$this->db->query();
				}
			}

			$query = "DROP TABLE `#__xprofiles_manager`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
