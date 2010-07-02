<?php

class YGroupHelper
{
	/** 
	 * Returns true if the current user is a member or a manger of the group 
	 * $group.
	 *
	 * $group may be the ID number or the cn of the group.
	 *
	 * pass a user's ID number as the optional $uid to check that user instead 
	 * of the currently logged-in user
	 */
	public static function is_member($group, $uid = NULL)
	{
		return self::check_group_role('#__xgroups_members', $group, $uid);
	}

	public static function is_manager($group, $uid = NULL)
	{
		return self::check_group_role('#__xgroups_managers', $group, $uid);	
	}

	public static function is_applicant($group, $uid = NULL)
	{
		return self::check_group_role('#__xgroups_applicants', $group, $uid);
	}

	public static function is_invited($group, $uid = NULL)
	{
		return self::check_group_role('#__xgroups_invitees', $group, $uid);
	}
	
	/*
	 * Query the various user->group association tables to look for a given 
	 * relation. 
	 *
	 * TODO The fact that this is possible suggests that the schema should
	 * rather be one table with uid, gid, and role foreign keys.
	 */
	private static function check_group_role($tbl, $group, $uid)
	{
		if (!$uid)
		{
			$user =& JFactory::getUser();
			if ($user->guest) 
				return false;
			$uid = $user->get('id');
		}

		$dbh =& JFactory::getDBO();
		if (is_int($group))
			$dbh->setQuery('SELECT 1 FROM '.$tbl.' WHERE uidNumber = '.$uid.' AND gidNumber = '.$group.' LIMIT 1');
		else
			$dbh->setQuery(
				'SELECT COUNT(gu.uidNumber)
					FROM #__xgroups g
					LEFT JOIN '.$tbl.' gu 
						ON gu.uidNumber = '.$uid.'
						AND gu.gidNumber = g.gidNumber
					WHERE g.cn = '.$dbh->quote($group).'
					LIMIT 1'
			);
		return !!$dbh->loadResult();
	}
}

