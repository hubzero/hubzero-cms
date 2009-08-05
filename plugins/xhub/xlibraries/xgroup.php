<?php
/**
 * @package		HUBzero CMS
 * @author		Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

class XGroupHelper
{
	function valid_cn($gid) 
	{
		if (eregi("^[0-9a-zA-Z]+[_0-9a-zA-Z]*$", $gid)) {
			if (is_numeric($gid) && intval($gid) == $gid && $gid >= 0) {
				return false;
			} else {
				return true;
			}
		} else {
			return false;
		}
	}
	
	function valid_description($name) 
	{
		if (eregi("^[ \,\.\/0-9a-zA-Z-]+$", $name)) {
			return true;
		} else {
			return false;
		}
	}
	
	function groups_exists($group)
	{
		$db = &JFactory::getDBO();

		if (empty($group))
			return false;

		if (is_numeric($group))
			$query = 'SELECT gidNumber FROM #__xgroups WHERE gidNumber=' . $db->Quote($group);
		else
			$query = 'SELECT gidNumber FROM #__xgroups WHERE cn=' . $db->Quote($group);
			
		$db->setQuery($query);
		
		if (!$db->query())
			return false;

		if ($db->loadResult() > 0)
			return true;

		return false;
	}

	function get_groups($type='hub', $asGidNumbers=true, $filters=array())
	{
		$db = &JFactory::getDBO();

		if (!in_array($type, array('system','hub','project','all','0','1','2')))
			return false;

		if ($type == 'all')
			$where_clause = '';
		else
		{
			if ($type == 'system')
				$type = '0';
			elseif ($type == 'hub')
				$type = '1';
			elseif ($type == 'project')
				$type = '2';

			$where_clause = 'WHERE type=' . $db->Quote($type);
		}
		
		if (isset($filters['search']) && $filters['search'] != '') {
			if ($where_clause != '') {
				$where_clause .= " AND";
			} else {
				$where_clause = "WHERE";
			}
			$where_clause .= " (LOWER(description) LIKE '%".$filters['search']."%' OR LOWER(cn) LIKE '%".$filters['search']."%')";
		}
		
		if (isset($filters['index']) && $filters['index'] != '') {
			if ($where_clause != '') {
				$where_clause .= " AND";
			} else {
				$where_clause = "WHERE";
			}
			$where_clause .= " (LOWER(description) LIKE '".$filters['index']."%') ";
		}

		if (isset($filters['authorized']) && $filters['authorized']) {
			if ($filters['authorized'] === 'admin') {
				$where_clause .= "";
			} else {
				if ($where_clause != '') {
					$where_clause .= " AND";
				} else {
					$where_clause .= "WHERE";
				}
				$where_clause .= " privacy<=1";
			}
		} else {
			if ($where_clause != '') {
				$where_clause .= " AND";
			} else {
				$where_clause .= "WHERE";
			}
			$where_clause .= " privacy=0";
		}

		if ($asGidNumbers) {
			$filters['fields'][] = 'gidNumber';
		} else {
			if (!in_array('COUNT(*)',$filters['fields'])) {
				$filters['fields'][] = 'cn';
			}
		}
		
		$field = implode(',',$filters['fields']);
		
		$query = "SELECT $field FROM #__xgroups $where_clause";
		if (isset($filters['sortby']) && $filters['sortby'] != '') {
			$query .= " ORDER BY ".$filters['sortby'];
		}
		if (isset($filters['limit']) && $filters['limit'] != 'all') {
			$query .= " LIMIT ".$filters['start'].",".$filters['limit'];
		}
		$query .= ";";

		$db->setQuery($query);
		//$db->query();

		//$result = $db->loadResultArray();
		if (!in_array('COUNT(*)',$filters['fields'])) {
			$result = $db->loadObjectList();
		} else {
			$result = $db->loadResult();
		}
		
		if (empty($result))
			return false;

		return $result;
	}

	function _ldap_get_groups($type = 'hub', $asGidNumbers = true)
	{
		$conn =& XFactory::getLDC();
		$xhub = &XFactory::getHub();
        
		if(!$conn || !$xhub)
			return false;

        	$hubLDAPBaseDN = $xhub->getCfg('hubLDAPBaseDN');

	        $ldap_base_dn = "ou=groups,".$hubLDAPBaseDN;
        
		if ($type == 'system' || $type == '0') /* system=true, or system and closed not set */
                	$ldap_search_str = "(&(objectClass=posixGroup) (| (system=TRUE) (& (!(system=*)) (!(closed=*)) ) ))";
        	elseif($type == 'hub' || $type == '1') /* system=false and closed=false or not set, or system not set and closed=false */
                	$ldap_search_str = "(&(objectClass=posixGroup) (| (&(system=FALSE) (|(closed=FALSE) (!(closed=*)) ) ) (& (!(system=*))(closed=FALSE)) ))";
        	elseif($type == 'project' || $type == '2') /* closed = true and system = false or not set */
                	$ldap_search_str = "(&(objectClass=posixGroup)(closed=TRUE)(|(system=FALSE)(!(system=*))))";
        	elseif ($type == 'all') /* all groups */
                	$ldap_search_str = "(&(objectClass=posixGroup)(gid=*))";
		else /* same as hub by default */
                	$ldap_search_str = "(&(objectClass=posixGroup) (| (&(system=FALSE) (|(closed=FALSE) (!(closed=*)) ) ) (& (!(system=*))(closed=FALSE)) ))";

        	$getgroups = array();

        	$reqattr = array();
        	$reqattr[] = 'cn';
        	$reqattr[] = 'gidNumber';
        	$group = array();
        
		$groupentry = ldap_search($conn, $ldap_base_dn, $ldap_search_str, $reqattr, 0, 0, 0, 3);
        
		$entry = ldap_first_entry($conn, $groupentry);
        
		while($entry) 
		{
                	$attr = ldap_get_attributes($conn, $entry);
                
			if ($asGidNumbers)
				$list[] = (isset($attr[$reqattr[1]][0])) ? $attr[$reqattr[1]][0] : null;
			else
				$list[] = (isset($attr[$reqattr[0]][0])) ? $attr[$reqattr[0]][0] : null;
                
			$entry = ldap_next_entry($conn, $entry);
        	}

		if (empty($list))
			return array();
        
		return $list;
	}
}

class XGroup extends JObject
{
	var $gidNumber;
	var $cn;
	var $description;
	var $published;
	var $type;
	var $access;
	var $public_desc;
	var $private_desc;
	var $restrict_msg;
	var $join_policy;
	var $privacy;

	var $_lists = array();
	var $_keys = array('gidNumber', 'cn', 'description', 'published', 'type', 'access', 'public_desc', 'private_desc', 'restrict_msg', 'join_policy', 'privacy');
	var $_list_keys = array('members','managers','applicants','invitees');

	public function logDebug($msg)
	{
		$xlog =& XFactory::getLogger();
		$xlog->logDebug($msg);
	}

	private function _clear_lists()
	{
		unset($this->_lists);
		$this->_lists['add']['managers'] = array();
		$this->_lists['add']['applicants'] = array();
		$this->_lists['add']['members'] = array();
		$this->_lists['add']['invitees'] = array();
		$this->_lists['delete']['managers'] = array();
		$this->_lists['delete']['applicants'] = array();
		$this->_lists['delete']['members'] = array();
		$this->_lists['delete']['invitees'] = array();

		return true;
	}

	private function _clear()
	{
		unset($this->gidNumber);
		unset($this->cn);
		unset($this->description);
		unset($this->published);
		unset($this->type);
		unset($this->access);
		unset($this->public_desc);
		unset($this->private_desc);
		unset($this->restrict_msg);
		unset($this->join_policy);
		unset($this->privacy);

		$this->gidNumber = null;
		$this->cn = null;
		$this->description = null;
		$this->published = null;
		$this->type = null;
		$this->access = null;
		$this->public_desc = null;
		$this->private_desc = null;
		$this->restrict_msg = null;
		$this->join_policy = null;
		$this->privacy = null;

		$this->_clear_lists();

		return true;
	}

	public function select($group)
	{
		$db = &JFactory::getDBO();

		if (empty($group))
			$group = $this->gidNumber;

		if (empty($group))
			$group = $this->cn;

		if (empty($group))
			return false;

		if (is_numeric($group))
			$query = "SELECT * FROM #__xgroups WHERE gidNumber = " . $db->Quote( intval($group) ) . ";";
		else
			$query = "SELECT * FROM #__xgroups WHERE cn = " . $db->Quote( $group ) . ";";

		$db->setQuery($query);

		$result = $db->loadAssoc();

		if (empty($result))
			return false;

		$this->gidNumber = $result['gidNumber'];
		$this->cn = $result['cn'];
		$this->description = $result['description'];
		$this->published = $result['published'];
		$this->type = $result['type'];
		$this->access = $result['access'];
		$this->public_desc = $result['public_desc'];
		$this->private_desc = $result['private_desc'];
		$this->restrict_msg = $result['restrict_msg'];
		$this->join_policy = $result['join_policy'];
		$this->privacy = $result['privacy'];

		return true;
	}

	public function __construct($group = null)
	{
		//$this->logDebug("XGroup::__construct($group)");
		$this->_clear();
		$this->select($group);
	}

	private function _getUsername($id)
	{
		// Initialize some variables
		$db = & JFactory::getDBO();
		
		$query = 'SELECT username FROM #__users WHERE id = ' . $db->Quote( $id );
		$db->setQuery($query, 0, 1);
		return $db->loadResult();
	}

	public function _ldap_create()
	{
		if ($this->gidNumber <= 0)
			return false;

		$xhub = &XFactory::getHub();
        	$conn = &XFactory::getPLDC();
        
		if (!$conn || !$xhub)
                	return false;

		if (empty($this->cn) || empty($this->gidNumber))
			return false;

        	$hubLDAPBaseDN = $xhub->getCfg('hubLDAPBaseDN');

        	$dn = 'gid=' . $this->cn . ',ou=groups,' . $hubLDAPBaseDN;

        	$attr = array();
        	$attr["objectclass"] = array();
        	$attr["objectclass"][0] = "top";
        	$attr["objectclass"][1] = "posixGroup";
        	$attr["objectclass"][2] = "hubGroup";
        	$attr['gid'] = $this->cn;
        	$attr['gidNumber'] = $this->gidNumber;
        	$attr['cn'] = $this->cn;

		if (!empty($this->description))
		{
        		$attr['description'] = $this->description;
        		$attr['groupName'] = $this->description;
		}

                $attr['public'] = ($this->published) ? 'TRUE' : 'FALSE';

                switch($this->type)
                {
			default:
			case '0':
                        case 'system':
                                $attr['system'] = 'TRUE';
                                $attr['closed'] = 'TRUE';
                                break;
			case '1':
                        case 'hub':
                                $attr['system'] = 'FALSE';
                                $attr['closed'] = 'FALSE';
                                break;
			case '2':
                        case 'project':
                                $attr['system'] = 'FALSE';
                                $attr['closed'] = 'TRUE';
                                break;
                }

                if (empty($this->access))
			$attr['privacy'] = 0;
                elseif ($this->access == 0)
			$attr['privacy'] = 0;
		elseif ($this->access == 3)
			$attr['privacy'] = 1;
		elseif ($this->access == 4)
			$attr['privacy'] = 2;
		else
			$attr['privacy'] = 0;

		if (is_array($this->_lists['add']['members']))
		{
			foreach($this->_lists['add']['members'] as $member)
			{
				if (is_numeric($member))
					$uid = $this->_getUsername($member);
				else
					$uid = $member;

        			$memberdn = 'uid=' . $uid . ',ou=users,' . $hubLDAPBaseDN;
				$attr['member'][] = $memberdn;
			}
		}

		if (is_array($this->_lists['add']['managers']))
		{
			foreach($this->_lists['add']['managers'] as $member)
			{
				if (is_numeric($member))
					$uid = $this->_getUsername($member);
				else
					$uid = $member;

        			$memberdn = 'uid=' . $uid . ',ou=users,' . $hubLDAPBaseDN;
				$attr['owner'][] = $memberdn;
			}
		}

		if (is_array($this->_lists['add']['applicants']))
		{
			foreach($this->_lists['add']['applicants'] as $member)
			{
				if (is_numeric($member))
					$uid = $this->_getUsername($member);
				else
					$uid = $member;

        			$memberdn = 'uid=' . $uid . ',ou=users,' . $hubLDAPBaseDN;
				$attr['applicant'][] = $memberdn;
			}
		}

        	if (@ldap_add($conn, $dn, $attr))
                	return true;

		$this->setError( 'Error creating group ' . $this->cn );

		return false;
	}

	public function insert()
	{
		$db = &JFactory::getDBO();
		$xhub = &XFactory::getHub();

		$gconfig = & JComponentHelper::getParams( 'com_groups' );
		$ldapGroupMirror = $gconfig->get('ldapGroupMirror');

		if (empty($db))
			return false;

		if (!empty($this->gidNumber) && ($this->gidNumber > 0))
		{
			$query = "INSERT INTO #__xgroups (gidNumber,cn,description,published,type,access,public_desc,private_desc,restrict_msg,join_policy,privacy) VALUES ( " .
				$db->Quote($this->gidNumber) . "," .
				$db->Quote($this->cn) . "," .
				$db->Quote($this->description) . "," .
				$db->Quote($this->published) . "," .
				$db->Quote($this->type) . "," .
				$db->Quote($this->access) . "," .
				$db->Quote($this->public_desc) . "," .
				$db->Quote($this->private_desc) . "," .
				$db->Quote($this->restrict_msg) . "," .
				$db->Quote($this->join_policy) . "," .
				$db->Quote($this->privacy) . ");";
		}
		else
		{
			$query = "INSERT INTO #__xgroups (cn,description,published,type,access,public_desc,private_desc,restrict_msg,join_policy,privacy) VALUES ( " .
				$db->Quote($this->cn) . "," .
				$db->Quote($this->description) . "," .
				$db->Quote($this->published) . "," .
				$db->Quote($this->type) . "," .
				$db->Quote($this->access) . "," .
				$db->Quote($this->public_desc) . "," .
				$db->Quote($this->private_desc) . "," .
				$db->Quote($this->restrict_msg) . "," .
				$db->Quote($this->join_policy) . "," .
				$db->Quote($this->privacy) . ");";
		}

		$db->setQuery( $query );
			           
		if (!$db->query())
			return false;

		if (empty($this->gidNumber) || ($this->gidNumber <= 0))
			$this->gidNumber = $db->insertId();
    
		// ensure there were no references to this group

		$query = "DELETE FROM #__xgroups_members WHERE gidNumber=" . $db->Quote($this->gidNumber);
		$db->setQuery($query);
		$db->query();

		$query = "DELETE FROM #__xgroups_applicants WHERE gidNumber=" . $db->Quote($this->gidNumber);
		$db->setQuery($query);
		$db->query();

		$query = "DELETE FROM #__xgroups_managers WHERE gidNumber=" . $db->Quote($this->gidNumber);
		$db->setQuery($query);
		$db->query();

		$query = "DELETE FROM #__xgroups_invitees WHERE gidNumber=" . $db->Quote($this->gidNumber);
		$db->setQuery($query);
		$db->query();

		// insert any users that have been added to this group

		$list = array();
		foreach ($this->_lists['add']['members'] as $user) 
		{
			if (is_numeric($user)) {
				$list[] = $user;
			}
		}
		
		if (count($list) > 0) {
			$list = implode($list,",");
			
			$query = "INSERT INTO #__xgroups_members (gidNumber, uidNumber) SELECT '" . 
					$this->gidNumber . "',id FROM #__users WHERE id IN (" . $list . ");";

			$db->setQuery( $query );
			$db->query();	
		}

		$list = array();
		foreach ($this->_lists['add']['applicants'] as $user) 
		{
			if (is_numeric($user)) {
				$list[] = $user;
			}
		}
		
		if (count($list) > 0) {
			$list = $db->Quote(implode($list,"','"));
			
			$query = "INSERT IGNORE INTO #__xgroups_applicants (gidNumber, uidNumber) SELECT '" .
					$this->gidNumber . "',id FROM #__users WHERE id IN (" . $list . ");";
			
			$db->setQuery( $query );
			$db->query();
		}

		$list = array();
		foreach ($this->_lists['add']['managers'] as $user) 
		{
			if (is_numeric($user)) {
				$list[] = $user;
			}
		}
		
		if (count($list) > 0) {
			$list = $db->Quote(implode($list,"','"));
			
			$query = "INSERT IGNORE INTO #__xgroups_managers (gidNumber, uidNumber) SELECT '" .
					$this->gidNumber . "',id FROM #__users WHERE id IN (" . $list . ");";
			
			$db->setQuery( $query );
			$db->query();
		}

		$list = array();
		foreach($this->_lists['add']['invitees'] as $user) 
		{
			if (is_numeric($user)) {
				$list[] = $user;
			}
		}
		
		if (count($list) > 0) {
			$list = $db->Quote(implode($list,"','"));
			
			$query = "INSERT IGNORE INTO #__xgroups_invitees (gidNumber, uidNumber) SELECT '" .
					$this->gidNumber . "',id FROM #__users WHERE id IN (" . $list . ");";
			
			$db->setQuery( $query );
			$db->query();
       }

       	if ($ldapGroupMirror)
			$this->_ldap_create();

		$this->_clear_lists();
	}

	private function _getUidFromDN($dn) 
	{
		if (strncmp($dn,"uid=",4) != 0)
			return false;

		$endpos = strpos($dn,',',4);

		if ($endpos)
			return substr($dn,4,$endpos-4);
		else
			return substr($dn,4);
	}

	public function _ldap_get_group($group) 
	{
		$xhub = &XFactory::getHub();
        	$conn = &XFactory::getPLDC();
        
		if (!$conn || !$xhub || empty($group))
                	return false;

        	$hubLDAPBaseDN = $xhub->getCfg('hubLDAPBaseDN');
                        
                if(is_numeric($group)) 
                        $dn = "ou=groups," . $hubLDAPBaseDN;
                else
                        $dn = "gid=$group,ou=groups," . $hubLDAPBaseDN;
                                
                $reqattr = array();
                $reqattr[] = 'gid';
                $reqattr[] = 'groupName';
                $reqattr[] = 'description';
                $reqattr[] = 'public';
                $reqattr[] = 'privacy'; 
                $reqattr[] = 'system';
                $reqattr[] = 'closed';
                $reqattr[] = 'gidNumber';
                $reqattr[] = 'cn';

                if(is_numeric($group)) 
                        $groupentry = ldap_search($conn, $dn, "(&(objectClass=posixGroup)(gidNumber=" . $group . "))", $reqattr, 0, 0, 0, 3);
                else 
                        $groupentry = ldap_search($conn, $dn, "(objectClass=posixGroup)", $reqattr, 0, 0, 0, 3);
                
		if (empty($groupentry))
                        return null;

                $count = ldap_count_entries($conn, $groupentry);
                
		if ($count <= 0)
			return null;

                $firstentry = ldap_first_entry($conn, $groupentry);
                $attr = ldap_get_attributes($conn, $firstentry);

                $groupinfo = array();
                $groupinfo['gid'] = isset($attr[$reqattr[0]][0]) ? $attr[$reqattr[0]][0] : false;
                $groupinfo['groupName'] = isset($attr[$reqattr[1]][0]) ? $attr[$reqattr[1]][0] : false;
                $groupinfo['description'] = isset($attr[$reqattr[2]][0]) ? $attr[$reqattr[2]][0] : false;
                $groupinfo['public'] = isset($attr[$reqattr[3]][0]) ? $attr[$reqattr[3]][0] : false;
		$groupinfo['privacy'] = isset($attr[$reqattr[4]][0]) ? $attr[$reqattr[4]][0] :  false;
		$groupinfo['system'] = isset($attr[$reqattr[5]][0]) ? $attr[$reqattr[5]][0] : false;
		$groupinfo['closed'] = isset($attr[$reqattr[6]][0]) ? $attr[$reqattr[6]][0] : false;
                $groupinfo['gidNumber'] = isset($attr[$reqattr[7]][0]) ? $attr[$reqattr[7]][0] : false;
                $groupinfo['cn'] = isset($attr[$reqattr[8]][0]) ? $attr[$reqattr[8]][0] : false;
		return($groupinfo);
	}

	public function _ldap_load($group)
	{
		$groupinfo = $this->_ldap_get_group($group);

		if ($groupinfo === false)
			return false;

		if (!is_numeric($groupinfo['gidNumber']))
			return false;

		if (empty($groupinfo['gid']) && empty($groupinfo['cn']))
			return false;

		$this->gidNumber = $groupinfo['gidNumber'];

		if (!empty($groupinfo['gid']))
			$this->cn = $groupinfo['gid'];
		else
			$this->cn = $groupinfo['cn'];

		if ($groupinfo['description'] !== false)
			$this->description = $groupinfo['description'];
		
		if ($groupinfo['description'] == $this->cn && !empty($groupinfo['groupName']))
			$this->description = $groupinfo['groupName'];

		if ($groupinfo['description'] === false || $groupinfo['description'] == '[none]')
			$this->description = $groupinfo['groupName'];

		if (empty($this->description))
			$this->description = '';

		$this->published = ($groupinfo['public'] == 'TRUE') ? '1' : '0';
		
		if ($groupinfo['system'] == 'TRUE' || ($groupinfo['closed'] === false && $groupinfo['system'] === false))
			$this->type = '0'; // system
		elseif ( ($groupinfo['system'] == 'FALSE' && ($groupinfo['closed'] == 'FALSE' || $groupinfo['closed'] === false)) || ($groupinfo['system'] === false && $groupinfo['closed'] == 'FALSE'))
			$this->type = '1'; // hub
		elseif ( ($groupinfo['closed'] == 'TRUE') && ($groupinfo['system'] === false || $groupinfo['system'] === 'FALSE'))
			$this->type = '2'; // project
		else
			$this->type = 1;

		if ($groupinfo['privacy'] === false)
			$this->access = '0';
		if ($groupinfo['privacy'] == '0' || $groupinfo['privacy'] == 'public')
			$this->access = '0';
		else if ($groupinfo['privacy'] == '1' || $groupinfo['privacy'] == 'protected')
			$this->access = '3';
		else if ($groupinfo['privacy'] == '2' || $groupinfo['privacy'] == 'private')
			$this->access = '4';
		else
			$this->access = '0';

		return true;
	}

	private function _ldap_rename_gid($oldgid, $newgid)
        {
                $ldapconn =& XFactory::getPLDC();
                $xhub =& XFactory::getHub();

                $hubLDAPBaseDN = $xhub->getCfg('hubLDAPBaseDN');

                if (!$ldapconn)
                        return false;

                $dn = 'gid=' . $oldgid . ',ou=groups,' . $hubLDAPBaseDN;
                $rdn = 'gid=' . $newgid;

                ldap_rename($ldapconn, $dn, $rdn, 'ou=groups,' . $hubLDAPBaseDN,true);
        }

	private function _ldap_update()
	{
		$xhub = &XFactory::getHub();
        	$conn = &XFactory::getPLDC();
        	$errno = 0;

		if (!$conn || !$xhub)
                	return(0);

        	$hubLDAPBaseDN = $xhub->getCfg('hubLDAPBaseDN');

		$groupinfo = $this->_ldap_get_group($this->gidNumber);

		if (($this->cn != $groupinfo['gid']) && !empty($groupinfo['gid']) && !empty($this->cn))
			$this->_ldap_rename_gid($groupinfo['gid'], $this->cn);

		$dn = 'gid=' . $this->cn . ',ou=groups,' . $hubLDAPBaseDN;
                
		$replace_attr = array();
		$add_attr = array();
		$delete_attr = array();

                /* gidNumber ... can not be modified as it is the primary key */

		if ($groupinfo['cn'] !== false && !empty($this->cn))
			$replace_attr['cn'] = $this->cn;
		elseif ($groupinfo['cn'] !== false && empty($this->cn))
			$delete_attr[] = 'cn'; // actually this will fail
		elseif ($groupinfo['cn']  === false && !empty($this->cn))
			$add_attr['cn'] = $this->cn;

		if ($groupinfo['gid'] !== false && !empty($this->cn))
			$replace_attr['gid'] = $this->cn;
		elseif ($groupinfo['gid'] !== false && empty($this->cn))
			$delete_attr[] = 'gid';
		elseif ($groupinfo['gid']  === false && !empty($this->cn))
			$add_attr['gid'] = $this->cn;

		if ($groupinfo['groupName'] !== false && !empty($this->description))
			$replace_attr['groupName'] = $this->description;
		elseif ($groupinfo['groupName'] !== false && empty($this->description))
			$delete_attr[] = 'groupName';
		elseif ($groupinfo['groupName']  === false && !empty($this->description))
			$add_attr['groupName'] = $this->description;

		if ($groupinfo['description'] !== false && !empty($this->description))
			$replace_attr['description'] = $this->description;
		elseif ($groupinfo['description'] !== false && empty($this->description))
			$delete_attr[] = 'description';
		elseif ($groupinfo['description']  === false && !empty($this->description))
			$add_attr['description'] = $this->description;

		$public = ($this->published) ? 'TRUE' : 'FALSE';

		if ($groupinfo['public'] !== false)
			$replace_attr['public'] = $public;
		else
			$add_attr['public'] = $public;

                switch($this->type)
                {
			default:
			case '0':
                        case 'system':
                                $system = 'TRUE';
                                $closed = 'TRUE';
                                break;

			case '1':
                        case 'hub':
                                $system = 'FALSE';
                                $closed = 'FALSE';
                                break;

			case '2':
                        case 'project':
                                $system = 'FALSE';
                                $closed = 'TRUE';
                                break;
                }

		if ($groupinfo['system'] !== false)
			$replace_attr['system'] = $system;
		else
			$add_attr['system'] = $system;

		if ($groupinfo['closed'] !== false)
			$replace_attr['closed'] = $closed;
		else
			$add_attr['closed'] = $closed;

                if (empty($this->access))
			$privacy = 0;
               	elseif ($this->access == 0)
			$privacy = 0;
		elseif ($this->access == 3)
			$privacy = 1;
		elseif ($this->access == 4)
			$privacy = 2;
		else
			$privacy = 0;

		if ($groupinfo['privacy'] !== false)
			$replace_attr['privacy'] = $privacy;
		else
			$add_attr['privacy'] = $privacy;

		if (is_array($this->_lists['add']['members']))
                {
                        foreach($this->_lists['add']['members'] as $member)
                        {
                                if (is_numeric($member))
                                        $uid = $this->_getUsername($member);
                                else
                                        $uid = $member;

                                $attr = array( 'member' => 'uid=' . $uid . ',ou=users,' . $hubLDAPBaseDN );
        			
				if (!@ldap_mod_add($conn, $dn, $attr))
				{
					$errno = @ldap_errno($conn);
					if ($errno == 20) // ignore already exists error
						$errno = 0;
				}
			}
                }

                if (is_array($this->_lists['add']['managers']))
                {
                        foreach($this->_lists['add']['managers'] as $member)
                        {
                                if (is_numeric($member))
                                        $uid = $this->_getUsername($member);
                                else
                                        $uid = $member;

                                $attr = array( 'owner' => 'uid=' . $uid . ',ou=users,' . $hubLDAPBaseDN );
        			
				if (!@ldap_mod_add($conn, $dn, $attr))
				{
					$errno = @ldap_errno($conn);
					if ($errno == 20) // ignore already exists error
						$errno = 0;
				}
                        }
                }

                if (is_array($this->_lists['add']['applicants']))
                {
                        foreach($this->_lists['add']['applicants'] as $member)
                        {
				if ($errno == 20) // ignore already exists error
					$errno = 0;

                                if (is_numeric($member))
                                        $uid = $this->_getUsername($member);
                                else
                                        $uid = $member;

                                $attr = array( 'applicant' => 'uid=' . $uid . ',ou=users,' . $hubLDAPBaseDN );
        			
				if (!@ldap_mod_add($conn, $dn, $attr))
				{
					$errno = @ldap_errno($conn);
					if ($errno == 20) // ignore already exists error
						$errno = 0;
				}
                        }
                }

                if (is_array($this->_lists['delete']['members']))
                {
                        foreach($this->_lists['delete']['members'] as $member)
                        {
                                if (is_numeric($member))
                                        $uid = $this->_getUsername($member);
                                else
                                        $uid = $member;

                                $attr = array( 'member' => 'uid=' . $uid . ',ou=users,' . $hubLDAPBaseDN );

        			if (!@ldap_mod_del($conn, $dn, $attr))
				{
					$errno = @ldap_errno($conn);
					if ($errno == 16) // ignore if it doesn't exist
						$errno = 0;
				}
                        }
                }

                if (is_array($this->_lists['delete']['managers']))
                {
                        foreach($this->_lists['delete']['managers'] as $member)
                        {
                                if (is_numeric($member))
                                        $uid = $this->_getUsername($member);
                                else
                                        $uid = $member;

                                $attr = array( 'owner' => 'uid=' . $uid . ',ou=users,' . $hubLDAPBaseDN );
        			
				if (!@ldap_mod_del($conn, $dn, $attr))
				{
					$errno = @ldap_errno($conn);
					if ($errno == 16) // ignore if it doesn't exist
						$errno = 0;
				}
                        }
                }

                if (is_array($this->_lists['delete']['applicants']))
                {
                        foreach($this->_lists['delete']['applicants'] as $member)
                        {
                                if (is_numeric($member))
                                        $uid = $this->_getUsername($member);
                                else
                                        $uid = $member;

                                $attr = array( 'applicant' => 'uid=' . $uid . ',ou=users,' . $hubLDAPBaseDN );
        			
				if (!@ldap_mod_del($conn, $dn, $attr))
				{
					$errno = @ldap_errno($conn);
					if ($errno == 16) // ignore if it doesn't exist
						$errno = 0;
				}
                        }
                }

        	if (!@ldap_mod_replace($conn, $dn, $replace_attr))
			$errno = @ldap_errno($conn);
        	if (!@ldap_mod_add($conn, $dn, $add_attr))
			$errno = @ldap_errno($conn);
        	if (!@ldap_mod_del($conn, $dn, $delete_attr))
			$errno = @ldap_errno($conn);
		
		if ($errno != 0)
			return false;

		return true;
	}

	function update()
	{ 
		$db = &JFactory::getDBO();
		$xhub = &XFactory::getHub();

		$gconfig = & JComponentHelper::getParams( 'com_groups' );
		$ldapGroupMirror = $gconfig->get('ldapGroupMirror');

		$query = "UPDATE #__xgroups SET " .
			"cn=" . $db->Quote($this->cn) .
			",description=" . $db->Quote($this->description) .
			",published=" . $db->Quote($this->published) .
			",type=" . $db->Quote($this->type) . 
			",access=" . $db->Quote($this->access) .
			",public_desc=" . $db->Quote($this->public_desc) .
			",private_desc=" . $db->Quote($this->private_desc) .
			",restrict_msg=" . $db->Quote($this->restrict_msg) .
			",join_policy=" . $db->Quote($this->join_policy) .
			",privacy=" . $db->Quote($this->privacy) .
			" WHERE gidNumber=" . $db->Quote($this->gidNumber) .
			";";

		$db->setQuery( $query );
			           
		if (!$db->query())
		{
			echo "update failed";
			return false;
		}

		if (is_array($this->_lists['add']['members']))
		{
			$list = array();
			foreach ($this->_lists['add']['members'] as $user) 
			{
				if (is_numeric($user)) {
					$list[] = $user;
				}
			}
			$list = implode($list,"','");

			$query = "INSERT IGNORE INTO #__xgroups_members (gidNumber,uidNumber) SELECT " . $db->Quote($this->gidNumber) . ",id FROM #__users WHERE id IN (" . stripslashes($db->Quote($list)) . ");";
			
			$db->setQuery( $query );
			$result = $db->query();
		}
                        
		if (is_array($this->_lists['add']['applicants']))
		{
			$list = array();
			foreach ($this->_lists['add']['applicants'] as $user) 
			{
				if (is_numeric($user)) {
					$list[] = $user;
				}
			}
			
			$list = implode($list,"','");
			
			$query = "INSERT IGNORE INTO #__xgroups_applicants (gidNumber, uidNumber) SELECT '" .
					$this->gidNumber . "',id FROM #__users WHERE id IN (" . stripslashes($db->Quote($list)) . ");";
                
			$db->setQuery( $query );
			$db->query();
		}
                
		if (is_array($this->_lists['add']['managers'])) 
		{
			$list = array();
			foreach ($this->_lists['add']['managers'] as $user) 
			{
				if (is_numeric($user)) {
					$list[] = $user;
				}
			}
			
			$list = implode($list,"','");
			
			$query = "INSERT IGNORE INTO #__xgroups_managers (gidNumber, uidNumber) SELECT '" .
					$this->gidNumber . "',id FROM #__users WHERE id IN (" . stripslashes($db->Quote($list)) . ");";
			
			$db->setQuery( $query );
			$db->query();
		}
                
		if (is_array($this->_lists['add']['invitees']))
		{
			$list = array();
			foreach ($this->_lists['add']['invitees'] as $user) 
			{
				if (is_numeric($user)) {
					$list[] = $user;
				}
			}
			
			$list = implode($list,"','");
			
			$query = "INSERT IGNORE INTO #__xgroups_invitees (gidNumber, uidNumber) SELECT '" .
					$this->gidNumber . "',id FROM #__users WHERE id IN (" . stripslashes($db->Quote($list)) . ");";
                
			$db->setQuery( $query );
			$db->query();
		}

		if (is_array($this->_lists['delete']['members']))
		{
			$members = array_unique($this->_lists['delete']['members']);
			$list = implode($members,"','");
			$query = "DELETE FROM #__xgroups_members WHERE gidNumber=" . $db->Quote($this->gidNumber) . " AND uidNumber IN (" . stripslashes($db->Quote($list)) . ");";
			$db->setQuery( $query );
			$db->query();
		}
                        
		if (is_array($this->_lists['delete']['applicants']))
		{
			$applicants = array_unique($this->_lists['delete']['applicants']);
			$list = implode($applicants,"','");
			$query = "DELETE FROM #__xgroups_applicants WHERE gidNumber=" . $db->Quote($this->gidNumber) . " AND uidNumber IN (" . stripslashes($db->Quote($list)) . ");";
			$db->setQuery( $query );
			$db->query();
		}
                
		if (is_array($this->_lists['delete']['managers']))
		{
			$managers = array_unique($this->_lists['delete']['managers']);
			$list = implode($managers,"','");
			$query = "DELETE FROM #__xgroups_managers WHERE gidNumber=" . $db->Quote($this->gidNumber) . " AND uidNumber IN (" . stripslashes($db->Quote($list)) . ");";
			$db->setQuery( $query );
			$db->query();
		}
                
		if (is_array($this->_lists['delete']['invitees']))
		{
			$invitees = array_unique($this->_lists['delete']['invitees']);
			$list = implode($invitees,"','");
			$query = "DELETE FROM #__xgroups_invitees WHERE gidNumber=" . $db->Quote($this->gidNumber) . " AND uidNumber IN (" . stripslashes($db->Quote($list)) . ");";
			$db->setQuery( $query );
			$db->query();
		}

		if ($ldapGroupMirror)
			$this->_ldap_update();

		$this->_clear_lists();
	}

	function save()
	{
		$db = &JFactory::getDBO();

		$query = "SELECT gidNumber FROM #__xgroups WHERE gidNumber=" . $db->Quote($this->gidNumber);

		$db->setQuery($query);

		$insert = false;
		
		if (!$db->query())
			$insert = true;

		$result = $db->loadResult();

		if (($result != $this->gidNumber) || !$result)
			$insert = true;

		if ($insert == true)
			$this->insert();
		else
			$this->update();
	}

	function getInstance($group = null)
	{
		//$this->logDebug("XGroup::getInstance($group)");
		$instance = new XGroup($group);
		
		$gid = $instance->get('gidNumber');

		if (empty($gid))
		    return false;
		
		return $instance;
	}

	public function is_member_of($table, $uid)
	{
		$db   =& JFactory::getDBO();

		if (!is_numeric($uid))
			$uidNumber = JUserHelper::getUserId($uid);
		else
			$uidNumber = $uid;

		if (!in_array($table, array('applicants','members','managers','invitees')))
			return false;

		if (in_array($uidNumber, $this->_lists['delete'][$table]))
			return false;

		if (in_array($uidNumber, $this->_lists['add'][$table]))
			return true;

		$table = '#__xgroups_' . $table;

		$query = "SELECT * FROM $table WHERE gidNumber=" . $db->Quote($this->gidNumber) . " AND uidNumber=" . $db->Quote($uidNumber) . ";";

		$db->setQuery($query);
		//$db->query();

		$result = $db->loadResultArray();

		return !empty($result);
	}

	public function isMember($uid)
	{
		return $this->is_member_of('members',$uid);
	}

	public function isApplicant($uid)
	{
		return $this->is_member_of('applicants',$uid);
	}

	public function isManager($uid)
	{
		return $this->is_member_of('managers',$uid);
	}

	public function isInvitee($uid)
	{
		return $this->is_member_of('invitees',$uid);
	}

	public function _ldap_get_members($table, $asUidNumbers = true)
	{
		$xhub = &XFactory::getHub();
        	$conn = &XFactory::getPLDC();

		if (!$conn || !$xhub)
                	return false;

		if (is_numeric($this->gidNumber))
			$group = $this->gidNumber;
		else
			$group = $this->cn;

		if (empty($group))
			return false;

        	$hubLDAPBaseDN = $xhub->getCfg('hubLDAPBaseDN');
                        
                if(is_numeric($group)) 
                        $dn = "ou=groups," . $hubLDAPBaseDN;
                else
                        $dn = "gid=$group,ou=groups," . $hubLDAPBaseDN;
 
                $reqattr = array();

		if ($table == 'applicants')
                	$reqattr[] = 'applicant';
		elseif ($table == 'members')
			$reqattr[] = 'member';
		elseif ($table == 'managers')
			$reqattr[] = 'owner';
		elseif ($table == 'invitees')
			return array();
		else
			return false;

                if(is_numeric($group)) 
                        $groupentry = ldap_search($conn, $dn, "(&(objectClass=posixGroup)(gidNumber=" . $group . "))", $reqattr, 0, 0, 0, 3);
                else 
                        $groupentry = ldap_search($conn, $dn, "(objectClass=posixGroup)", $reqattr, 0, 0, 0, 3);
                
		if (empty($groupentry))
                        return false;

                $count = ldap_count_entries($conn, $groupentry);
                
		if ($count <= 0)
			return false;

                $firstentry = ldap_first_entry($conn, $groupentry);
                $attr = ldap_get_attributes($conn, $firstentry);

                $uidlist = array();

                if(isset($attr[$reqattr[0]][0])) 
		{
                        for($i = 0; $i < count($attr[$reqattr[0]]); $i++) 
			{
                        	if (isset($attr[$reqattr[0]][$i]))
	                                array_push($uidlist, $this->_getUidFromDN($attr[$reqattr[0]][$i]));
                        }
		}

		if (!$asUidNumbers)	
			return $uidlist;

		$uidNumberList = array();

		foreach($uidlist as $uid)
				$uidNumberList[] =  JUserHelper::getUserId($uid);

		return($uidNumberList);
	}

	public function get($key, $modifier = null)
	{
		//$this->logDebug("XGroup::get($key)");

		if (in_array($key, $this->_keys))
			return $this->$key;

		if (!in_array($key, array('applicants','members','managers','invitees')))
			return false;

		$table = '#__xgroups_' . $key;

		if ($modifier === null)
			$asUidNumbers = true;
		else
			$asUidNumbers = $modifier;

		if ($asUidNumbers)
			$field = "u.id";
		else
			$field = "u.username";

		$db = & JFactory::getDBO();
		
		$query = "SELECT $field FROM $table AS t,#__users AS u WHERE t.gidNumber=" . $db->Quote($this->gidNumber) . " AND u.id=t.uidNumber;";

		$db->setQuery($query);
		//$db->query();

		$result = $db->loadResultArray();
		$result = (is_array($result)) ? array_unique($result) : array();

		$result = array_merge($result, $this->_lists['add'][$key]);
		$result = array_diff($result, $this->_lists['delete'][$key]);

		return $result;
	}

	public function set($key,$value) 
	{
		//$this->logDebug("XGroup::set($key,$value)");

		if (in_array($key, $this->_keys))
			$this->$key = $value;

		if (!in_array($key, array('applicants','members','managers','invitees')))
			return false;

		$this->remove( $this->get($key) );
		$this->add( $value );
	}

	public function add($key = 'members', $value)
	{
		jimport('joomla.user.helper');
		//$this->logDebug("XGroup::add($key,$value)");

		if (!in_array($key, array('members','applicants','managers','invitees')))
			return false;
		
		if (empty($value))
			return true;

		if (!is_array($value))
			$value = array( $value );

		$value = array_unique($value);

		foreach($value as $user)
		{
			if (!is_numeric($user))
				$user = JUserHelper::getUserId($user);
			
			if (!in_array($user,$this->_lists['add'][$key]))
			{
				$this->_lists['add'][$key][] = $user;
				$this->_lists['delete'][$key] = array_diff( $this->_lists['delete'][$key], array($user));
			}
		}

		return true;
	}

	public function remove($key, $value)
	{
		//$this->logDebug("XGroup::remove($key,$value)");

		if (!in_array($key, array('members','applicants','managers','invitees')))
			return false;

		if (empty($value))
			return true;

		if (!is_array($value))
			$value = array( $value );
		
		jimport('joomla.user.helper');
		
		foreach($value as $user)
		{
			if (!is_numeric($user))
			{
				$juserid = JUserHelper::getUserId($user);

				if (!empty($juserid))
					$user = $juserid;
			}

			if (!in_array($user,$this->_lists['delete'][$key]))
			{
				$this->_lists['delete'][$key][] = $user;
				$this->_lists['add'][$key] = array_diff( $this->_lists['add'][$key], array($user));
			}
		}

		return true;
	}

	public function _ldap_delete() 
	{
		$xhub =& XFactory::getHub();
        	$conn =& XFactory::getPLDC();

                if (empty($conn) || empty($xhub))
                        return false;

		if (!empty($this->gidNumber) && is_numeric($this->gidNumber))
			$gid = $this->gidNumber;
		else
			$gid = $this->cn;

		$groupinfo = $this->_ldap_get_group($gid);

		if (empty($groupinfo) || empty($groupinfo['gid']))
			return false;

        	$dn = "gid=" . $groupinfo['gid'] . ",ou=groups," . $xhub->getCfg('hubLDAPBaseDN');

        	if (!@ldap_delete($conn, $dn)) 
                	return false;

        	return true;
	}

	public function delete()
	{
		$errmsg = '';
		$query = null;
		$xuser = null;
		$db    = null;

		$xhub = &XFactory::getHub();

		$gconfig = & JComponentHelper::getParams( 'com_groups' );
		$ldapGroupMirror = $gconfig->get('ldapGroupMirror');

		//$this->logDebug("XGroup::delete()");

		if (!$this->gidNumber) {
			$this->setError( 'Error deleting group: no gidNumber.' );
			return false;
		}

		$xuser =& XFactory::getUser();
		
		$errmsg = '';

		$db = & JFactory::getDBO();

		if (empty($db)) {
			$this->setError( 'Error deleting group: no database.' );
			return false;
		}
		
		// Delete applicants
		$query = "DELETE FROM #__xgroups_applicants WHERE gidNumber = '" . $this->gidNumber . "'";
		$db->setQuery( $query );
		if (!$db->query())
			$errmsg .= "Error deleting group applicants: " . $db->getErrorMsg() . "\n";
		
		// Delete invitees
		$query = "DELETE FROM #__xgroups_invitees WHERE gidNumber = '" . $this->gidNumber . "'";
		$db->setQuery( $query );
		if (!$db->query())
			$errmsg .= "Error deleting group members: " . $db->getErrorMsg() . "\n";
		
		// Delete members
		$query = "DELETE FROM #__xgroups_members WHERE gidNumber = '" . $this->gidNumber . "'";
		$db->setQuery( $query );
		if (!$db->query())
			$errmsg .= "Error deleting group members: " . $db->getErrorMsg() . "\n";
		
		// Delete managers
		$query = "DELETE FROM #__xgroups_managers WHERE gidNumber = '" . $this->gidNumber . "'";
		$db->setQuery( $query );
		if (!$db->query())
			$errmsg .= "Error deleting group managers: " . $db->getErrorMsg() . "\n";
		
		// Delete the group last in case anything else goes wrong
		$query = "DELETE FROM #__xgroups WHERE gidNumber = '" . $this->gidNumber . "'";
		$db->setQuery( $query );
		if (!$db->query())
			$errmsg .= "Error deleting group: " . $db->getErrorMsg() . "\n";
		
		if (!empty($errmsg))
		{
			$this->setError($errmsg);
			return false;
		}

		if ($ldapGroupMirror)
			$this->_ldap_delete();
	
		$this->_clear();

		return true;
	}
	
	public function getEmails($key='managers') 
	{
		$emails = array();
		$users = $this->get($key);
		if ($users) {
			foreach ($users as $user) 
			{
				$u =& XUser::getInstance($user);
				if (is_object($u)) {
					$emails[] = $u->get('email');
				}
			}
		}
		return $emails;
	}
}
?>
