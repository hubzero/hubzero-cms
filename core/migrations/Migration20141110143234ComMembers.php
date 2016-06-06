<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

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

				foreach ($types as $alias => $title)
				{
					$query = "INSERT INTO `#__xorganization_types` (`id`, `type`, `title`) VALUES (NULL, " . $this->db->quote($alias) . ",". $this->db->quote($title) . ")";
					$this->db->setQuery($query);
					$this->db->query();
				}
			}
		}
	}
}
