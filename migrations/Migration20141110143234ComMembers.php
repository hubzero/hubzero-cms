<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding default member organization types
 **/
class Migration20141110143234ComMembers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__xorganization_types'))
		{
			$query = "SELECT COUNT(*) FROM `#__xorganization_types`";
			$this->db->setQuery($query);
			if (!$this->db->loadResult())
			{
				$types = array(
					'universityundergraduate' => 'University / College Undergraduate',
					'universitygraduate'      => 'University / College Graduate Student',
					'universityfaculty'       => 'University / College Faculty',
					'universitystaff'         => 'University / College Staff',
					'precollegestudent'       => 'K-12 (Pre-College) Student',
					'precollegefacultystaff'  => 'K-12 (Pre-College) Faculty/Staff',
					'nationallab'             => 'National Laboratory',
					'industry'                => 'Industry / Private Company',
					'government'              => 'Government Agency',
					'military'                => 'Military',
					'unemployed'              => 'Retired / Unemployed'
				);

				include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_members' . DS . 'tables' . DS . 'organizationtype.php');

				foreach ($types as $alias => $title)
				{
					$row = new MembersTableOrganizationType($this->db);
					$row->type  = $alias;
					$row->title = $title;
					$row->store();
				}
			}
		}
	}
}