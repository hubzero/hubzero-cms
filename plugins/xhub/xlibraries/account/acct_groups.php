<?php
/**
 * @package		HUBzero CMS
 * @author		Kevin Colby <colbykd@purdue.edu>
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

ximport('account.acct_inc');


function acc_getgroup($group) {
	global $hub;

	$conn =& XFactory::getLDC();
	if(!$conn) {
		return(null);
	}

	if($group) {
		if(is_positiveint($group)) {
			$ldap_base_dn = "ou=groups," . $hub['ldap_base'];
		}
		else {
			$ldap_base_str = "gid=[group],ou=groups," . $hub['ldap_base'];
			$ldap_base_dn = str_replace("[group]", $group, $ldap_base_str);
		}

		$reqattr = array();
		$reqattr[] = 'gid';
		//$reqattr[] = 'gidNumber';
		$reqattr[] = 'groupName';
		$reqattr[] = 'description';
		$reqattr[] = 'public';
		$reqattr[] = 'privacy';
		$reqattr[] = 'system';
		$reqattr[] = 'closed';
		$reqattr[] = 'owner';
		$reqattr[] = 'member';
		$groupinfo = array();
		if(is_positiveint($group)) {
			$groupentry = ldap_search($conn, $ldap_base_dn, "(gidNumber=" . $group . ")", $reqattr, 0, 0, 0, 3);
		}
		else {
			$groupentry = @ldap_search($conn, $ldap_base_dn, "(objectclass=*)", $reqattr, 0, 0, 0, 3);
			//echo "<p>[" . $ldap_base_dn . "]</p>\n";
		}
		if (empty($groupentry)) {
			$count = 0;
		}
		else {
			$count = ldap_count_entries($conn, $groupentry);
		} 
		if($count > 0) {
			$firstentry = ldap_first_entry($conn, $groupentry);
			$attr = ldap_get_attributes($conn, $firstentry);
			$groupinfo['gid'] = $attr[$reqattr[0]][0];
			//$groupinfo['gidNumber'] = $attr[$reqattr[1]][0];
			$groupinfo['name'] = $attr[$reqattr[1]][0];
			if(isset($attr[$reqattr[2]][0])) {
				$groupinfo['description'] = $attr[$reqattr[2]][0];
			}
			else {
				$groupinfo['description'] = '';
			}
			if(isset($attr[$reqattr[3]][0])) {
				if($attr[$reqattr[3]][0] == 'TRUE') {
					$groupinfo['confirmed'] = $attr[$reqattr[3]][0];
				}
				else {
					$groupinfo['confirmed'] = false;
				}
			}
			else {
				$groupinfo['confirmed'] = false;
			}
			if(isset($attr[$reqattr[4]][0])) {
				if($attr[$reqattr[4]][0] == 2) {
					$groupinfo['privacy'] = 'private';
				}
				elseif($attr[$reqattr[4]][0] == 1) {
					$groupinfo['privacy'] = 'protected';
				}
				else {
					$groupinfo['privacy'] = 'public';
				}
			}
			else {
				$groupinfo['privacy'] = 'public';
			}
			if(isset($attr[$reqattr[5]][0])) {
				if($attr[$reqattr[5]][0] == 'TRUE') {
					$groupinfo['system'] = true;
				}
				else {
					$groupinfo['system'] = false;
				}
			}
			else {
				$groupinfo['system'] = false;
			}
			if(isset($attr[$reqattr[6]][0])) {
				if($attr[$reqattr[6]][0] == 'TRUE') {
					$groupinfo['closed'] = true;
				}
				else {
					$groupinfo['closed'] = false;
				}
			}
			else {
				$groupinfo['closed'] = false;
			}
			if(isset($attr[$reqattr[7]][0])) {
				$groupinfo['owner'] = array();
				for($i = 0; $i < count($attr[$reqattr[7]]); $i++) {
					if(isset($attr[$reqattr[7]][$i])) {
						array_push($groupinfo['owner'], acc_extractfromdn($attr[$reqattr[7]][$i]));
					}
				}
			}
			if(isset($attr[$reqattr[8]][0])) {
				$groupinfo['member'] = array();
				for($i = 0; $i < count($attr[$reqattr[8]]); $i++) {
					if(isset($attr[$reqattr[8]][$i])) {
						array_push($groupinfo['member'], acc_extractfromdn($attr[$reqattr[8]][$i]));
					}
				}
			}
			return($groupinfo);
		}
	}
	return(null);
}


function acc_groupexists($name, $gid) {
	global $hub;

	$conn =& XFactory::getLDC();
	if(!$conn || !$name || !$gid) {
		return(0);
	}

	$ldap_base_dn = "ou=groups," . $hub['ldap_base'];
	$ldap_search_str = "(|(groupName=[name])(gid=[gid]))";

	$ldap_search_str = str_replace("[name]", $name, $ldap_search_str);
	$ldap_search_str = str_replace("[gid]", $gid, $ldap_search_str);
	$reqattr = array();
	$entry = ldap_search($conn, $ldap_base_dn, $ldap_search_str, $reqattr, 0, 0, 0, 3);
	$count = ldap_count_entries($conn, $entry);
	
	return ($count > 0);
}


function acc_getgroupmanagersemail($gid) {
	global $hub;

	$conn =& XFactory::getLDC();
	if(!$conn) {
		return(null);
	}

	$ldap_group_str = "gid=[gid],ou=groups," . $hub['ldap_base'];
	$ldap_search_str = "(owner=*)";

	$managersemail = '';

	$ldap_group_dn = str_replace("[gid]", $gid, $ldap_group_str);
	$reqattr = array('owner');
	$groupentry = ldap_search($conn, $ldap_group_dn, $ldap_search_str, $reqattr, 0, 0, 0, 3);
	$entry = ldap_first_entry($conn, $groupentry);
	$ldap_owner_dns = array();
	if($entry) {
		$attr = ldap_get_attributes($conn, $entry);
		for($i = 0; $i < count($attr[$reqattr[0]]); $i++) {
			if($attr[$reqattr[0]]) {
				array_push($ldap_owner_dns, $attr[$reqattr[0]][$i]);
			}
		}
	}
	foreach($ldap_owner_dns as $ldap_owner_dn) {
		if($ldap_owner_dn) {
			$reqattr = array('mail');
			$userentry = ldap_search($conn, $ldap_owner_dn, "(objectclass=*)", $reqattr, 0, 0, 0, 3);
			$entry = ldap_first_entry($conn, $userentry);
			$ldap_owner_dns = array();
			if($entry) {
				$attr = ldap_get_attributes($conn, $entry);
				if($managersemail) {
					$managersemail .= ',';
				}
				$managersemail .= $attr[$reqattr[0]][0];
			}
		}
	}

	return($managersemail);
}


function acc_usergroupmanage($adm, $login, $gid) {
	global $hub;

	$conn =& XFactory::getPLDC();
	if(!$conn || !$login || !$gid) {
		return(0);
	}

	$ldap_mod_str = "gid=[gid],ou=groups," . $hub['ldap_base'];
	$ldap_user_str = "uid=[login],ou=users," . $hub['ldap_base'];

	$ldap_mod_dn = str_replace("[gid]", $gid, $ldap_mod_str);
	$ldap_user_dn = str_replace("[login]", $login, $ldap_user_str);
	$attr = array('owner' => $ldap_user_dn);
	
	if(ldap_mod_add($conn, $ldap_mod_dn, $attr)) {
		$logstr = "gid=" . quote($gid);

		acc_log('user_group_setmanager', $adm, $login, $logstr);

		$attr = array('member' => $ldap_user_dn);

		return(ldap_mod_del($conn, $ldap_mod_dn, $attr));
	}

	return(0);
}


function acc_usergroupunmanage($adm, $login, $gid) {
	global $hub;

	$conn =& XFactory::getPLDC();
	if(!$conn || !$login || !$gid) {
		return(0);
	}

	$ldap_mod_str = "gid=[gid],ou=groups," . $hub['ldap_base'];
	$ldap_user_str = "uid=[login],ou=users," . $hub['ldap_base'];

	$ldap_mod_dn = str_replace("[gid]", $gid, $ldap_mod_str);
	$ldap_user_dn = str_replace("[login]", $login, $ldap_user_str);
	$attr = array('member' => $ldap_user_dn);
	if(ldap_mod_add($conn, $ldap_mod_dn, $attr)) {
		$logstr = "gid=" . quote($gid);
		acc_log('user_group_unsetmanager', $adm, $login, $logstr);
		$attr = array('owner' => $ldap_user_dn);
		return(ldap_mod_del($conn, $ldap_mod_dn, $attr));
	}
	return(0);
}


function acc_getgroupusers($gid, $usertype) {
	global $hub;

	$conn =& XFactory::getLDC();
	if(!$conn || !$gid) {
		return(0);
	}

	$ldap_base_dn = "ou=groups," . $hub['ldap_base'];
	$ldap_search_str = "(gid=[gid])";

	$ldap_search_str = str_replace("[gid]", $gid, $ldap_search_str);
	if($usertype == 0) {
		$groupreqattr = array('applicant');
	}
	elseif($usertype == 1) {
		$groupreqattr = array('member');
	}
	elseif($usertype == 2) {
		$groupreqattr = array('owner');
	}
	$groupentry = ldap_search($conn, $ldap_base_dn, $ldap_search_str, $groupreqattr, 0, 0, 0, 3);
	$count = ldap_count_entries($conn, $groupentry);

	if($count > 0) {
		$entry = ldap_first_entry($conn, $groupentry);
		$groupattr = ldap_get_attributes($conn, $entry);
		$groupusers = array();
		$userreqattr = array();
		$userreqattr[] = 'uid';
		$userreqattr[] = 'emailConfirmed';
		$userreqattr[] = 'cn';
		$userreqattr[] = 'mail';
		$userreqattr[] = 'o';
		$userreqattr[] = 'description';
		if(isset($groupattr[$groupreqattr[0]])) {
		for($i = 0; $i < count($groupattr[$groupreqattr[0]]); $i++) {
			$ldap_user_dn = (isset($groupattr[$groupreqattr[0]][$i])) ? $groupattr[$groupreqattr[0]][$i] : '';
			if($ldap_user_dn) {
				$userentry = ldap_search($conn, $ldap_user_dn, "(objectclass=*)", $userreqattr, 0, 0, 0, 3);
				$count = ldap_count_entries($conn, $userentry);
				if($count > 0) {
					$firstentry = ldap_first_entry($conn, $userentry);
					$attr = ldap_get_attributes($conn,  $firstentry);
					$user = array();
					$user['login'] = $attr[$userreqattr[0]][0];
					$user['email_confirmed'] = $attr[$userreqattr[1]][0];
					$user['name'] = $attr[$userreqattr[2]][0];
					$user['email'] = $attr[$userreqattr[3]][0];
					$user['org'] = $attr[$userreqattr[4]][0];
					$user['reason'] = isset($attr[$userreqattr[5]][0]) ? $attr[$userreqattr[5]][0] : '';
					array_push($groupusers, $user);
				}
			}
		}
		}
		return($groupusers);
	}

	return(null);
}


function acc_getgroups($confirmed = 1, $system = "", $closed = "") {
	global $hub;

	$conn =& XFactory::getLDC();
	if(!$conn) {
		return(array());
	}

	$ldap_base_dn = "ou=groups,".$hub['ldap_base'];
	if($closed === true && $system === true) {
		$ldap_search_str = "(&(closed=TRUE)(system=TRUE))";
	}
	elseif($closed === true && $system === false) {
		$ldap_search_str = "(&(closed=TRUE)(|(system=FALSE)(!(system=*))))";
	}
	elseif($closed === false && $system === true) {
		$ldap_search_str = "(&(system=TRUE)(|(closed=FALSE)(!(closed=*))))";
	}
	elseif($closed === false && $system === false) {
		$ldap_search_str = "(&(|(system=FALSE)(!(system=*)))(|(closed=FALSE)(!(closed=*))))";
	}
	elseif($closed === true) {
		$ldap_search_str = "(closed=TRUE)";
	}
	elseif($closed === false) {
		$ldap_search_str = "(|(closed=FALSE)(!(closed=*)))";
	}
	elseif($system === true) {
		$ldap_search_str = "(system=TRUE)";
	}
	elseif($system === false) {
		$ldap_search_str = "(|(system=FALSE)(!(system=*)))";
	}
	else {
		$ldap_search_str = "(gid=*)";
	}

	$getgroups = array();

	$reqattr = array();
	$reqattr[] = 'gid';
	$reqattr[] = 'groupName';
	$reqattr[] = 'description';
	$reqattr[] = 'public';
	$reqattr[] = 'privacy';
	$reqattr[] = 'system';
	$reqattr[] = 'closed';
	$reqattr[] = 'owner';
	$reqattr[] = 'member';
	$group = array();
	$groupentry = ldap_search($conn, $ldap_base_dn, $ldap_search_str, $reqattr, 0, 0, 0, 3);
	$entry = ldap_first_entry($conn, $groupentry);
	while($entry) {
		$attr = ldap_get_attributes($conn, $entry);
		$group['gid'] = (isset($attr[$reqattr[0]][0])) ? $attr[$reqattr[0]][0] : '';
		$group['name'] = (!empty($attr[$reqattr[1]][0])) ? $attr[$reqattr[1]][0] : '';
		$group['description'] = (!empty($attr[$reqattr[2]][0])) ? $attr[$reqattr[2]][0] : '';
		if(isset($attr[$reqattr[3]][0])) {
			if($attr[$reqattr[3]][0] == 'TRUE') {
				$group['confirmed'] = $attr[$reqattr[3]][0];
			}
			else {
				$group['confirmed'] = false;
			}
		}
		else {
			$group['confirmed'] = false;
		}
		if(isset($attr[$reqattr[4]][0])) {
			if($attr[$reqattr[4]][0] == 2) {
				$group['privacy'] = 'private';
			}
			elseif($attr[$reqattr[4]][0] == 1) {
				$group['privacy'] = 'protected';
			}
			else {
				$group['privacy'] = 'public';
			}
		}
		else {
			$group['privacy'] = 'public';
		}
		if(isset($attr[$reqattr[5]][0])) {
			if($attr[$reqattr[5]][0] == 'TRUE') {
				$group['system'] = true;
			}
			else {
				$group['system'] = false;
			}
		}
		else {
			$group['system'] = false;
		}
		if(isset($attr[$reqattr[6]][0])) {
			if($attr[$reqattr[6]][0] == 'TRUE') {
				$group['closed'] = true;
			}
			else {
				$group['closed'] = false;
			}
		}
		else {
			$group['closed'] = false;
		}
		if(isset($attr[$reqattr[7]][0])) {
			$group['owner'] = array();
			for($i = 0; $i < count($attr[$reqattr[7]]); $i++) {
				if(isset($attr[$reqattr[7]][$i])) {
					array_push($group['owner'], acc_extractfromdn($attr[$reqattr[7]][$i]));
				}
			}
		}
		if(isset($attr[$reqattr[8]][0])) {
			$group['member'] = array();
			for($i = 0; $i < count($attr[$reqattr[8]]); $i++) {
				if(isset($attr[$reqattr[8]][$i])) {
					array_push($group['member'], acc_extractfromdn($attr[$reqattr[8]][$i]));
				}
			}
		}
		if($confirmed && $group['confirmed'] || !$confirmed) {
			array_push($getgroups, $group);
		}
		$entry = ldap_next_entry($conn, $entry);
	}
	arraykeyedsort($getgroups, array('name'));

	return($getgroups);
}


function acc_groupcreate($adm, $name, $description, $gid, $system = false, $closed = false, $privacy = 'public', $owner = null, $member = null) {
	global $hub;

	$conn =& XFactory::getPLDC();
	$gid_number = acc_nextgid();
	if(!$conn || !$gid || !$name || !$gid_number) {
		return(null);
	}

	$ldap_add_str = "gid=[gid],ou=groups," . $hub['ldap_base'];
	$ldap_user_str = "uid=[login],ou=users," . $hub['ldap_base'];

	$ldap_add_dn = str_replace("[gid]", $gid, $ldap_add_str);
	$ldap_user_dn = str_replace("[login]", $adm, $ldap_user_str);
	$attr = array();
	$attr["objectclass"] = array();
	$attr["objectclass"][0] = "top";
	$attr["objectclass"][1] = "posixGroup";
	$attr["objectclass"][2] = "hubGroup";
	$attr['gid'] = $gid;
	$attr['gidNumber'] = $gid_number;
	$attr['cn'] = $gid;
	$attr['description'] = $description;
	$attr['groupName'] = $name;
	$attr['owner'] = $ldap_user_dn;
	$attr['public'] = 'TRUE';
	$attr["tracperm"] = array();
	$attr["tracperm"][0] = "WIKI_ADMIN";
	$attr["tracperm"][1] = "TICKET_ADMIN";
	$attr["tracperm"][2] = "REPORT_ADMIN";
	$attr["tracperm"][3] = "MILESTONE_ADMIN";
	$attr["tracperm"][4] = "BROWSER_VIEW";
	$attr["tracperm"][5] = "LOG_VIEW";
	$attr["tracperm"][6] = "FILE_VIEW";
	$attr["tracperm"][7] = "CHANGESET_VIEW";
	$attr["tracperm"][8] = "ROADMAP_VIEW";
	$attr["tracperm"][9] = "TIMELINE_VIEW";
	$attr["tracperm"][10] = "SEARCH_VIEW";
	if($system) {
		$attr['system'] = 'TRUE';
	}
	else {
		$attr['system'] = 'FALSE';
	}
	if($closed) {
		$attr['closed'] = 'TRUE';
	}
	else {
		$attr['closed'] = 'FALSE';
	}
	if($privacy == 'private') {
		$attr['privacy'] = 2;
	}
	elseif($privacy == "protected") {
		$attr['privacy'] = 1;
	}
	else {
		$attr['privacy'] = 0;
	}
	if($owner) {
		if(is_array($owner)) {
			$attr['owner'] = array();
			foreach($owner as $uid) {
				array_push($attr['owner'], str_replace("[login]", $uid, $ldap_user_str));
			}
		}
		else {
			$attr['owner'] = str_replace("[login]", $owner, $ldap_user_str);
		}
	}
	if($member) {
		if(is_array($member)) {
			$attr['member'] = array();
			foreach($member as $uid) {
				array_push($attr['member'], str_replace("[login]", $uid, $ldap_user_str));
			}
		}
		else {
			$attr['member'] = str_replace("[login]", $owner, $ldap_user_str);
		}
	}

	if(ldap_add($conn, $ldap_add_dn, $attr)) {
		$keys = array_keys($attr);
		$logstr = '';
		foreach($keys as $key){
			if(is_array($attr[$key])) {
				$subkeys = array_keys($attr[$key]);
				foreach($subkeys as $subkey){
					$logstr .= " " . $key . "[" . $subkey . "]=" . quote($attr[$key][$subkey]);
				}
			}
			else {
				$logstr .= " " . $key . "=" . quote($attr[$key]);
			}
		}
		$logstr = ltrim($logstr);

		acc_log('group_create', $adm, $gid, $logstr);
		
		return($gid);
	}

	return(null);
}


function acc_groupdelete($adm, $gid) {
	global $hub;

	$conn =& XFactory::getPLDC();
	if(!$conn || !$gid) {
		return(false);
	}

	$ldap_delete_str = "gid=[gid],ou=groups," . $hub['ldap_base'];

	$ldap_delete_dn = str_replace("[gid]", $gid, $ldap_delete_str);
	
	if(ldap_delete($conn, $ldap_delete_dn)) {
		acc_log('group_delete', $adm, $gid, '');
		return(true);
	}

	return(false);
}


function acc_groupconfirm($adm, $gid) {
	global $hub;

	$conn =& XFactory::getPLDC();
	if(!$conn || !$gid) {
		return(false);
	}

	$ldap_mod_str = "gid=[gid],ou=groups," . $hub['ldap_base'];

	$ldap_mod_dn = str_replace("[gid]", $gid, $ldap_mod_str);
	$attr = array('public' => 'TRUE');

	acc_log('group_confirm', $adm, $gid, '');

	if(ldap_mod_replace($conn, $ldap_mod_dn, $attr)) {
		return(1);
	}
	return(ldap_mod_add($conn, $ldap_mod_dn, $attr));
}


function acc_groupunconfirm($adm, $gid) {
	global $hub;

	$conn =& XFactory::getPLDC();
	if(!$conn || !$gid) {
		return(false);
	}

	$ldap_mod_str = "gid=[gid],ou=groups," . $hub['ldap_base'];

	$ldap_mod_dn = str_replace("[gid]", $gid, $ldap_mod_str);
	$attr = array('public' => 'FALSE');
	
	acc_log('group_unconfirm', $adm, $gid, '');
	
	if(ldap_mod_replace($conn, $ldap_mod_dn, $attr)) {
		return(1);
	}

	return(ldap_mod_add($conn, $ldap_mod_dn, $attr));
}


function acc_groupupdate($adm, $gid, $name, $description, $privacy, $system = "", $closed = "", $owner = null, $member = null) {
	global $hub;

	$conn =& XFactory::getPLDC();
	if(!$conn || !$gid) {
		return(0);
	}

	$ldap_mod_str = "gid=[gid],ou=groups," . $hub['ldap_base'];
	$ldap_user_str = "uid=[login],ou=users," . $hub['ldap_base'];

	$ldap_mod_dn = str_replace("[gid]", $gid, $ldap_mod_str);
	$groupinfo = acc_getgroup($gid);
	$attr = array();
	$delattr = array();
	if($name) {
		$attr['groupName'] = $name;
	}
	if($description) {
		$attr['description'] = $description;
	}
	elseif($groupinfo['description']) {
		$delattr['description'] = array();
	}
	if($privacy == 'private') {
		$attr['privacy'] = 2;
	}
	elseif($privacy == "protected") {
		$attr['privacy'] = 1;
	}
	else {
		$attr['privacy'] = 0;
	}
	if($system === true) {
		$attr['system'] = 'TRUE';
	}
	elseif($system === false) {
		$attr['system'] = 'FALSE';
	}
	if($closed === true) {
		$attr['closed'] = 'TRUE';
	}
	elseif($closed === false) {
		$attr['closed'] = 'FALSE';
	}
	if($owner) {
		if(is_array($owner)) {
			$attr['owner'] = array();
			foreach($owner as $uid) {
				array_push($attr['owner'], str_replace("[login]", $uid, $ldap_user_str));
			}
		}
		else {
			$attr['owner'] = str_replace("[login]", $owner, $ldap_user_str);
		}
	}
	elseif(is_array($owner) && count($groupinfo['owner'])) {
		$delattr['owner'] = array();
	}
	if($member) {
		if(is_array($member)) {
			$attr['member'] = array();
			foreach($member as $uid) {
				array_push($attr['member'], str_replace("[login]", $uid, $ldap_user_str));
			}
		}
		else {
			$attr['member'] = str_replace("[login]", $owner, $ldap_user_str);
		}
	}
	elseif(is_array($member) && count($groupinfo['member'])) {
		$delattr['member'] = array();
	}
	$keys = array_keys($attr);
	$logstr = '';
	foreach($keys as $key){
		if(is_array($attr[$key])) {
			$subkeys = array_keys($attr[$key]);
			foreach($subkeys as $subkey){
				$logstr .= " " . $key . "[" . $subkey . "]=" . quote($attr[$key][$subkey]);
			}
		}
		else {
			$logstr .= " " . $key . "=" . quote($attr[$key]);
		}
	}
	$logstr = ltrim($logstr);

	if(ldap_mod_replace($conn, $ldap_mod_dn, $attr)) {
		acc_log('group_update', $adm, $gid, $logstr);

		if(ldap_mod_del($conn, $ldap_mod_dn, $delattr)) {
			return(1);
		}
	}
	return(0);
}


function acc_usergroupreg($adm, $login, $gid) {
	global $hub;

	$conn =& XFactory::getPLDC();
	if(!$conn || !$gid || !$login) {
		return(0);
	}

	$ldap_mod_str = "gid=[gid],ou=groups," . $hub['ldap_base'];
	$ldap_user_str = "uid=[login],ou=users," . $hub['ldap_base'];

	$ldap_mod_dn = str_replace("[gid]", $gid, $ldap_mod_str);
	$ldap_user_dn = str_replace("[login]", $login, $ldap_user_str);
	$attr = array('applicant' => $ldap_user_dn);
	$logstr = "gid=" . quote($gid);

	acc_log('user_group_apply', $adm, $login, $logstr);

	return(ldap_mod_add($conn, $ldap_mod_dn, $attr));
}



function acc_group_userdel($adm, $type, $login, $gid) {
	global $hub;

	$conn =& XFactory::getPLDC();
	if(!$conn || !$gid || !$login) {
		return(0);
	}

	$ldap_mod_str = "gid=[gid],ou=groups," . $hub['ldap_base'];
	$ldap_user_str = "uid=[login],ou=users," . $hub['ldap_base'];

	$ret = 0;

	$ldap_mod_dn = str_replace("[gid]", $gid, $ldap_mod_str);
	$ldap_user_dn = str_replace("[login]", $login, $ldap_user_str);

	if ($type == 'manager')
		$type = 'owner';

	if (!in_array($type, array('applicant','member','owner','tracperm')))
	{
		acc_log('acc_group_userdel', $adm, $login, 'invalid attribute ' . $type);
		return false;
	}

	$filter = "$type=" . $ldap_user_dn;
	$entry = ldap_search($conn, $ldap_mod_dn, $filter, array(), 0, 0, 0, 3);
	$count = ldap_count_entries($conn, $entry);
	if($count) {
		$attr = array($type => $ldap_user_dn);
		if(ldap_mod_del($conn, $ldap_mod_dn, $attr)) {
			$ret = 1;
		}
	}

	if($ret) {
		$logstr = "gid=" . quote($gid);
		acc_log('userungroup', $adm, $login, $logstr);
	}
	else
		acc_log('acc_group_userdel', $adm, $login, 'ldap failed to delete ' . $type);

	return($ret);
}

function acc_usergroupunreg($adm, $login, $gid) {
	global $hub;

	$conn =& XFactory::getPLDC();
	if(!$conn || !$gid || !$login) {
		return(0);
	}

	$ldap_mod_str = "gid=[gid],ou=groups," . $hub['ldap_base'];
	$ldap_user_str = "uid=[login],ou=users," . $hub['ldap_base'];

	$ret = 0;

	$ldap_mod_dn = str_replace("[gid]", $gid, $ldap_mod_str);
	$ldap_user_dn = str_replace("[login]", $login, $ldap_user_str);

	$filter = "applicant=" . $ldap_user_dn;
	$entry = ldap_search($conn, $ldap_mod_dn, $filter, array(), 0, 0, 0, 3);
	$count = ldap_count_entries($conn, $entry);
	if($count) {
		$attr = array('applicant' => $ldap_user_dn);
		if(ldap_mod_del($conn, $ldap_mod_dn, $attr)) {
			$ret = 1;
		}
	}

	$filter = "member=" . $ldap_user_dn;
	$entry = ldap_search($conn, $ldap_mod_dn, $filter, array(), 0, 0, 0, 3);
	$count = ldap_count_entries($conn, $entry);
	if($count) {
		$attr = array('member' => $ldap_user_dn);
		if(ldap_mod_del($conn, $ldap_mod_dn, $attr)) {
			$ret = 1;
		}
	}

	$filter = "owner=" . $ldap_user_dn;
	$entry = ldap_search($conn, $ldap_mod_dn, $filter, array(), 0, 0, 0, 3);
	$count = ldap_count_entries($conn, $entry);
	if($count) {
		$attr = array('owner' => $ldap_user_dn);
		if(ldap_mod_del($conn, $ldap_mod_dn, $attr)) {
			$ret = 1;
		}
	}

	if($ret) {
		$logstr = "gid=" . quote($gid);
		acc_log('user_group_remove', $adm, $login, $logstr);
	}

	return($ret);
}


function acc_usergroupconfirm($adm, $login, $gid) {
	global $hub;

	$conn =& XFactory::getPLDC();
	if(!$conn || !$gid || !$login) {
		return(0);
	}

	$ldap_mod_str = "gid=[gid],ou=groups," . $hub['ldap_base'];
	$ldap_user_str = "uid=[login],ou=users," . $hub['ldap_base'];

	$ldap_mod_dn = str_replace("[gid]", $gid, $ldap_mod_str);
	$ldap_user_dn = str_replace("[login]", $login, $ldap_user_str);
	$attr = array('member' => $ldap_user_dn);

	if(ldap_mod_add($conn, $ldap_mod_dn, $attr)) {
		$logstr = "gid=" . quote($gid);
		acc_log('user_group_approve', $adm, $login, $logstr);
		$attr = array('applicant' => $ldap_user_dn);
		return(ldap_mod_del($conn, $ldap_mod_dn, $attr));
	}

	return(0);
}


function acc_usergroupsetmanager($adm, $login, $gid) {
	global $hub;

	$conn =& XFactory::getPLDC();
	if(!$conn || !$gid || !$login) {
		return(0);
	}

	$ldap_mod_str = "gid=[gid],ou=groups," . $hub['ldap_base'];
	$ldap_user_str = "uid=[login],ou=users," . $hub['ldap_base'];

	$ldap_mod_dn = str_replace("[gid]", $gid, $ldap_mod_str);
	$ldap_user_dn = str_replace("[login]", $login, $ldap_user_str);
	$attr = array('owner' => $ldap_user_dn);
	if(ldap_mod_add($conn, $ldap_mod_dn, $attr)) {
		$logstr = "gid=" . quote($gid);
		acc_log('user_group_setmanager', $adm, $login, $logstr);
		$attr = array('member' => $ldap_user_dn);
		return(ldap_mod_del($conn, $ldap_mod_dn, $attr));
	}

	return(0);
}


function acc_usergroupunsetmanager($adm, $login, $gid) {
	global $hub;

	$conn =& XFactory::getPLDC();
	if(!$conn || !$gid || !$login) {
		return(0);
	}

	$ldap_mod_str = "gid=[gid],ou=groups," . $hub['ldap_base'];
	$ldap_user_str = "uid=[login],ou=users," . $hub['ldap_base'];

	$ldap_mod_dn = str_replace("[gid]", $gid, $ldap_mod_str);
	$ldap_user_dn = str_replace("[login]", $login, $ldap_user_str);
	$attr = array('member' => $ldap_user_dn);

	if(ldap_mod_add($conn, $ldap_mod_dn, $attr)) {
		$logstr = "gid=" . quote($gid);
		acc_log('user_group_unsetmanager', $adm, $login, $logstr);
		$attr = array('owner' => $ldap_user_dn);
		return(ldap_mod_del($conn, $ldap_mod_dn, $attr));
	}

	return(0);
}


function acc_isusergroupmanager($login, $gid = null, $sole = false) {
	global $hub;

	$conn =& XFactory::getLDC();
	if(!$conn || !$gid || !$login) {
		return(false);
	}

	if(!empty($gid)) {
		$ldap_base_str = "gid=[gid],ou=groups," . $hub['ldap_base'];
	}
	else {
		$ldap_base_str = "ou=groups," . $hub['ldap_base'];
	}
	$ldap_search_str = "(owner=uid=[login],ou=users," . $hub['ldap_base'] . ")";

	$ldap_base_dn = str_replace("[gid]", $gid, $ldap_base_str);
	$ldap_search_str = str_replace("[login]", $login, $ldap_search_str);
	$reqattr = array();
	$userentry = ldap_search($conn, $ldap_base_dn, $ldap_search_str, $reqattr, 0, 0, 0, 3);
	$count = ldap_count_entries($conn, $userentry);

	if($count > 0) {
		if($sole) {
			$managers = acc_getgroupusers($gid, 2);
			if(sizeof($managers) == 1) {
				return(true);
			}
		}
		else {
			return(true);
		}
	}
	return(false);
}


function acc_getusergroupsmanager($login, $sole = false) {
	global $hub;

	$conn =& XFactory::getLDC();
	if(!$conn) {
		return(array());
	}

	$groups = acc_getusergroups($login);
	$managergroups = array();
	foreach($groups as $group){
		if($group['manager']) {
			if(!$sole || acc_isusergroupmanager($login, $group['gid'], true)) {
				array_push($managergroups, $group);
			}
		}
	}
	return($managergroups);
}


function acc_nextgid() {
    global $hub;

    $conn =& XFactory::getPLDC();

    if (!$conn)
        return;

	$ldap_dn = "cn=maxgid," . $hub['ldap_base'];
	$max_tries = 5;

	$tries = 0;
	$nextgid = 0;
	while(!$nextgid && $tries < $max_tries) {
		$tries++;
		$ldap_search_str = "(gidNumber=*)";
		$reqattr = array("gidNumber");
		$gidentry = ldap_search($conn, $ldap_dn, $ldap_search_str, $reqattr, 0, 0, 0, 3);
		$entry = ldap_first_entry($conn, $gidentry);
		if($entry) {
			$attr = ldap_get_attributes($conn, $entry);
			$nextgid = $attr[$reqattr[0]][0];
			if($nextgid) {
				$attr = array("lock" => $nextgid);
				$mod = ldap_mod_add($conn, $ldap_dn, $attr);
				if($mod) {
					$ldap_search_str = "(lock=" . $nextgid . ")";
					$reqattr = array("gidNumber");
					$gidentry = ldap_search($conn, $ldap_dn, $ldap_search_str, $reqattr, 0, 0, 0, 3);
					$entry = ldap_first_entry($conn, $gidentry);
					if($entry) {
						$attr = ldap_get_attributes($conn, $entry);
						if($nextgid == $attr[$reqattr[0]][0]) {
							$nextgid++;
							$attr = array("gidNumber" => $nextgid);
							if(!ldap_mod_replace($conn, $ldap_dn, $attr)) {
								$nextgid = 0;
							}
							$attr = array("lock" => array());
							ldap_mod_del($conn, $ldap_dn, $attr);
						}
					}
					else {
						$nextgid = 0;
						sleep(2);
					}
				}
				else {
					$nextgid = 0;
					sleep(2);
				}
			}
		}
	}
	return($nextgid);
}

?>
