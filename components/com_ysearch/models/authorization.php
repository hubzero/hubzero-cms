<?php

class YSearchAuthorization
{
	private $uid = NULL, $super_admin = false, $groups = NULL;

	public function __construct()
	{
		$juser =& JFactory::getUser();
		if ($juser->guest)
		{
			$this->groups = array();
			return;
		}
		$this->uid = $juser->get('id');
		if ($juser->usertype == 'Super Administrator')
			$this->super_admin = true;
	}

	public function is_guest() { return is_null($this->uid); }
	public function is_super_admin() { return $this->super_admin; }
	public function get_group_ids()
	{
		if (is_null($this->groups))
		{
			$dbh =& JFactory::getDBO();
			$dbh->setQuery(
				'select distinct gidNumber from #__xgroups_members where uidNumber = '.$this->uid.' union select distinct gidNumber from #__xgroups_managers where uidNumber = '.$this->uid
			);
			$this->groups = $dbh->loadResultArray();
		}
		return $this->groups;
	}
}
