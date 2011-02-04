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
	public function get_groups()
	{
		if (is_null($this->groups))
		{
			$dbh =& JFactory::getDBO();
			$dbh->setQuery(
				'select distinct xm.gidNumber, cn from #__xgroups_members xm inner join #__xgroups g on g.gidNumber = xm.gidNumber where uidNumber = '.$this->uid.' union select distinct xm.gidNumber, cn from #__xgroups_managers xm inner join #__xgroups g on g.gidNumber = xm.gidNumber where uidNumber = '.$this->uid
			);
	
			$this->groups = array();
			foreach ($dbh->loadAssocList() as $row)
				$this->groups[$row['gidNumber']] = $row['cn'];
		}
		return $this->groups;
	}
	public function get_group_ids()
	{
		return array_keys($this->get_groups());
	}
	public function get_group_names()
	{
		return array_values($this->get_groups());
	}
}
