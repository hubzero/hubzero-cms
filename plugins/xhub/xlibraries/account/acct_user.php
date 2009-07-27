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
ximport('account.acct_func');
ximport('misc_func');

function acc_getuser($login) {
    global $hub;

	$conn =& XFactory::getLDC();

	if(!$login) {
	    $juser =& JFactory::getUser();
		$login = $juser->get('username');
	}

	if($login) {
		if(is_positiveint($login)) {
			$ldap_base_dn = "ou=users," . $hub['ldap_base'];
		}
		else {
			$ldap_base_str = "uid=[login],ou=users," . $hub['ldap_base'];
			$ldap_base_dn = str_replace("[login]", $login, $ldap_base_str);
		}

		$reqattr = array();
		array_push($reqattr, "uid");
		array_push($reqattr, "uidNumber");
		array_push($reqattr, "userPassword");
		array_push($reqattr, "emailConfirmed");
		array_push($reqattr, "homeDirectory");
		array_push($reqattr, "jobsAllowed");
		array_push($reqattr, "regDate");
		array_push($reqattr, "regIp");
		array_push($reqattr, "regHost");
		array_push($reqattr, "cn");
		array_push($reqattr, "mail");
		array_push($reqattr, "o");
		array_push($reqattr, "orgtype");
		array_push($reqattr, "countryresident");
		array_push($reqattr, "countryorigin");
		array_push($reqattr, "sex");
		array_push($reqattr, "disability");
		array_push($reqattr, "hispanic");
		array_push($reqattr, "race");
		array_push($reqattr, "nativeTribe");
		array_push($reqattr, "url");
		array_push($reqattr, "homePhone");
		array_push($reqattr, "description");
		array_push($reqattr, "usageAgreement");
		array_push($reqattr, "admin");
		array_push($reqattr, "edulevel");
		array_push($reqattr, "role");
		array_push($reqattr, "collaborationMember");
		array_push($reqattr, "modDate");
		array_push($reqattr, "mailPreferenceOption");
		array_push($reqattr, "proxyUidNumber");
		array_push($reqattr, "proxyPassword");
		$user = array();
		if(is_positiveint($login)) {
			$userentry = @ldap_search($conn, $ldap_base_dn, "(uidNumber=" . $login . ")", $reqattr, 0, 0, 0, 3);
		}
		else {
			$userentry = @ldap_search($conn, $ldap_base_dn, "(objectclass=*)", $reqattr, 0, 0, 0, 3);
		}

		$count = empty($userentry) ? 0 : ldap_count_entries($conn, $userentry);
		if($count > 0) {
			$firstentry = ldap_first_entry($conn, $userentry);
			$attr = ldap_get_attributes($conn, $firstentry);
			$user['login'] = $attr[$reqattr[0]][0];
			$user['uid'] = $attr[$reqattr[1]][0];
			$user['encrypt_password'] = $attr[$reqattr[2]][0];
			$user['email_confirmed'] = (isset($attr[$reqattr[3]][0]) ? $attr[$reqattr[3]][0] : false);
			$user['home'] = $attr[$reqattr[4]][0];
			$user['jobs_allowed'] = (isset($attr[$reqattr[5]][0]) ? $attr[$reqattr[5]][0] : 0);
			$user['reg_date'] = $attr[$reqattr[6]][0];
			$user['reg_ip'] = (isset($attr[$reqattr[7]][0]) ? $attr[$reqattr[7]][0] : false);
			$user['reg_host'] = (isset($attr[$reqattr[8]][0]) ? $attr[$reqattr[8]][0] : false);
			$user['name'] = $attr[$reqattr[9]][0];
			$user['email'] = $attr[$reqattr[10]][0];
			$user['org'] = isset($attr[$reqattr[11]][0]) ? $attr[$reqattr[11]][0] : false;
			$user['orgtype'] = isset($attr[$reqattr[12]][0]) ? $attr[$reqattr[12]][0] : false;
			$user['countryresident'] = isset($attr[$reqattr[13]][0]) ? $attr[$reqattr[13]][0] : false;
			$user['countryorigin'] = isset($attr[$reqattr[14]][0]) ? $attr[$reqattr[14]][0] : false;
			$user['sex'] = isset($attr[$reqattr[15]][0]) ? $attr[$reqattr[15]][0] : false;
			$user['disability'] = array();
			if (isset($attr[$reqattr[16]])) {
				for($i = 0; $i < count($attr[$reqattr[16]]) - 1; $i++) {
					array_push($user['disability'], $attr[$reqattr[16]][$i]);
				}
			}
			$user['hispanic'] = array();
			if (isset($attr[$reqattr[17]])) {
				for($i = 0; $i < count($attr[$reqattr[17]]) - 1; $i++) {
					array_push($user['hispanic'], $attr[$reqattr[17]][$i]);
				}
			}
			$user['race'] = array();
			if (isset($attr[$reqattr[18]])) {
				for($i = 0; $i < count($attr[$reqattr[18]]) - 1; $i++) {
					array_push($user['race'], $attr[$reqattr[18]][$i]);
				}
			}
			$user['nativetribe'] = (isset($attr[$reqattr[19]][0]) ? $attr[$reqattr[19]][0] : false);
			$user['web'] = (isset($attr[$reqattr[20]][0]) ? $attr[$reqattr[20]][0] : false);
			$user['phone'] = (isset($attr[$reqattr[21]][0]) ? $attr[$reqattr[21]][0] : false);
			$user['reason'] = (isset($attr[$reqattr[22]][0]) ? $attr[$reqattr[22]][0] : false);
			if ( isset($attr[$reqattr[23]][0]) && ($attr[$reqattr[23]][0] == "TRUE")) {
				$user['usageAgreement'] = true;
			}
			else {
				$user['usageAgreement'] = false;
			}
			$user['admin'] = array();
			$count = isset($attr[$reqattr[24]]) ? count($attr[$reqattr[24]]) - 1 : 0;
			for($i = 0; $i < $count; $i++) {
				array_push($user['admin'], $attr[$reqattr[24]][$i]);
			}
			$user['edulevel'] = array();
			$count = isset($attr[$reqattr[25]]) ? count($attr[$reqattr[25]]) - 1 : 0;
			if (isset($attr[$reqattr[25]])) {
				for($i = 0; $i < $count; $i++) {
					array_push($user['edulevel'], $attr[$reqattr[25]][$i]);
				}
			}
			$user['role'] = array();
			$count = isset($attr[$reqattr[26]]) ? count($attr[$reqattr[26]]) - 1 : 0;
			if (isset($attr[$reqattr[26]])) {
				for($i = 0; $i < $count; $i++) {
					array_push($user['role'], $attr[$reqattr[26]][$i]);
				}
			}
			$usercollabgroup = (isset($attr[$reqattr[27]][0]) ? $attr[$reqattr[27]][0] : false);
			$user['mod_date'] = (isset($attr[$reqattr[28]][0]) ? $attr[$reqattr[28]][0] : false);
			$user['mailPreferenceOption'] = (isset($attr[$reqattr[29]][0]) ? $attr[$reqattr[29]][0] : 1);
			$user['proxy_uid'] = (isset($attr[$reqattr[30]][0]) ? $attr[$reqattr[30]][0] : false);
			$user['proxy_password'] = (isset($attr[$reqattr[31]][0]) ? $attr[$reqattr[31]][0] : false);
			$user['maxrooms'] = 0;
			if($usercollabgroup) {
				$reqattr = array("maxRooms");
				$groupentry = ldap_search($conn, $usercollabgroup, "(objectclass=*)", $reqattr, 0, 0, 0, 3);
				$count = ldap_count_entries($conn, $groupentry);
				if($count > 0) {
					$firstentry = ldap_first_entry($conn, $groupentry);
					$attr = ldap_get_attributes($conn, $firstentry);
					$user['maxrooms'] = $attr[$reqattr[0]][0];
				}
			}
			$user['groups'] = acc_getusergroups($user['login']);
			return($user);
		}
	}
	return(null);
}

function acc_getusergroups($login) {
    global $hub;

	$conn =& XFactory::getLDC();

    if (!$conn)
       return;

	$ldap_base_dn = "ou=groups," . $hub['ldap_base'];
	$ldap_owner_search_str = "(owner=uid=[login],ou=users," . $hub['ldap_base'] . ")";
	$ldap_member_search_str = "(member=uid=[login],ou=users," . $hub['ldap_base'] . ")";
	$ldap_applicant_search_str = "(applicant=uid=[login],ou=users," . $hub['ldap_base'] . ")";
	$ldap_group_search_str = "(objectclass=hubGroup)";

	$getusergroups = array();
	if($conn) {
		$group = array();

		$reqattr = array();
		array_push($reqattr, "gid");
		array_push($reqattr, "groupName");
		array_push($reqattr, "description");
		array_push($reqattr, "public");
		$filter = str_replace("[login]", $login, "(&" . $ldap_group_search_str . $ldap_applicant_search_str . ")");
		$groupentry = ldap_search($conn, $ldap_base_dn, $filter, $reqattr, 0, 0, 0, 3);
		$entry = ldap_first_entry($conn, $groupentry);
		while($entry) {
			$attr = ldap_get_attributes($conn, $entry);
			$group['gid'] = $attr[$reqattr[0]][0];
			$group['name'] = $attr[$reqattr[1]][0];
			$group['description'] = $attr[$reqattr[2]][0];
			$group['confirmed'] = $attr[$reqattr[3]][0];
			$group['manager'] = 0;
			$group['regconfirmed'] = 0;
			array_push($getusergroups, $group);
			$entry = ldap_next_entry($conn, $entry);
		}

		$reqattr = array();
		array_push($reqattr, "gid");
		array_push($reqattr, "groupName");
		array_push($reqattr, "description");
		array_push($reqattr, "public");
		$filter = str_replace("[login]", $login, "(&" . $ldap_group_search_str . $ldap_member_search_str . ")");
		$groupentry = ldap_search($conn, $ldap_base_dn, $filter, $reqattr, 0, 0, 0, 3);
		$entry = ldap_first_entry($conn, $groupentry);
		while($entry) {
			$attr = ldap_get_attributes($conn, $entry);
			$group['gid'] = (isset($attr[$reqattr[0]][0])) ? $attr[$reqattr[0]][0] : NULL;
			$group['name'] = (isset($attr[$reqattr[1]][0])) ? $attr[$reqattr[1]][0] : NULL;
			$group['description'] = (isset($attr[$reqattr[2]][0])) ? $attr[$reqattr[2]][0] : NULL;
			$group['confirmed'] = (isset($attr[$reqattr[3]][0])) ? $attr[$reqattr[3]][0] : NULL;
			$group['manager'] = 0;
			$group['regconfirmed'] = 1;
			array_push($getusergroups, $group);
			$entry = ldap_next_entry($conn, $entry);
		}

		$reqattr = array();
		array_push($reqattr, "gid");
		array_push($reqattr, "groupName");
		array_push($reqattr, "description");
		array_push($reqattr, "public");
		$filter = str_replace("[login]", $login, "(&" . $ldap_group_search_str . $ldap_owner_search_str . ")");
		$groupentry = ldap_search($conn, $ldap_base_dn, $filter, $reqattr, 0, 0, 0, 3);
		$entry = ldap_first_entry($conn, $groupentry);
		while($entry) {
			$attr = ldap_get_attributes($conn, $entry);
			$group['gid'] = (isset($attr[$reqattr[0]][0])) ? $attr[$reqattr[0]][0] : NULL;
			$group['name'] = (isset($attr[$reqattr[1]][0])) ? $attr[$reqattr[1]][0] : NULL;
			$group['description'] = (isset($attr[$reqattr[2]][0])) ? $attr[$reqattr[2]][0] : NULL;
			$group['confirmed'] = (isset($attr[$reqattr[3]][0])) ? $attr[$reqattr[3]][0] : NULL;
			$group['manager'] = 1;
			$group['regconfirmed'] = 1;
			array_push($getusergroups, $group);
			$entry = ldap_next_entry($conn, $entry);
		}
	}
	return($getusergroups);
}

function acc_userexists($login) {
    global $hub;

    $conn =& XFactory::getLDC();

    if (!$conn)
        return;

	$ldap_base_dn = "ou=users," . $hub['ldap_base'];
	$ldap_search_str = "(uid=[login])";

	$reserved_users = array(
		"adm",
		"alfred",
		"apache",
		"backup",
		"bin",
		"canna",
		"condor",
		"condor-util",
		"daemon",
		"debian-exim",
		"exim",
		"ftp",
		"games",
		"ganglia",
		"gnats",
		"gopher",
		"halt",
		"ibrix",
		"invigosh",
		"irc",
		"ldap",
		"list",
		"lp",
		"mail",
		"mailnull",
		"man",
		"nagios",
		"nanohub",
		"netdump",
		"news",
		"nfsnobody",
		"noaccess",
		"nobody",
		"nscd",
		"ntp",
		"operator",
		"openldap",
		"pcap",
		"postgres",
		"proxy",
		"pvm",
		"root",
		"rpc",
		"rpcuser",
		"rpm",
		"sag",
		"shutdown",
		"smmsp",
		"sshd",
		"statd",
		"sync",
		"sys",
		"uucp",
		"vcsa",
		"www",
		"www-data",
		"xfs"
		);

	if($login) {
		if(in_array(strtolower($login), $reserved_users)) {
			return(1);
		}
		else {
			$ldap_search_str = str_replace("[login]", $login, $ldap_search_str);
			$reqattr = array();
			$entry = ldap_search($conn, $ldap_base_dn, $ldap_search_str, $reqattr, 0, 0, 0, 3);
			$count = ldap_count_entries($conn, $entry);
			if($count > 0) {
				return(1);
			}
		}
	}
	return(0);
}

function acc_userlicense($adm, $login, $licenseid) {
    global $hub;

    $conn =& XFactory::getPLDC();

    if (!$conn)
        return;

	$ldap_license_str = "license=[licenseid],ou=licenses," . $hub['ldap_base'];
	$ldap_user_str = "uid=[login],ou=users," . $hub['ldap_base'];

	if($login && $licenseid) {
		$ldap_license_dn = str_replace("[licenseid]", $licenseid, $ldap_license_str);
		$ldap_user_dn = str_replace("[login]", $login, $ldap_user_str);
		$attr = array("member" => $ldap_license_dn);
		$logstr = "license=" . quote($licenseid);
		acc_log('license_grant', $adm, $login, $logstr);
		return(@ldap_mod_add($conn, $ldap_user_dn, $attr));
	}
	return(0);
}

function acc_userdelicense($adm, $login, $licenseid) {
    global $hub;
    $conn =& XFactory::getPLDC();

    if (!$conn)
        return 0;

	$ldap_license_str = "license=[licenseid],ou=licenses," . $hub['ldap_base'];
	$ldap_user_str = "uid=[login],ou=users," . $hub['ldap_base'];

	if($login && $licenseid) {
		$ldap_license_dn = str_replace("[licenseid]", $licenseid, $ldap_license_str);
		$ldap_user_dn = str_replace("[login]", $login, $ldap_user_str);
		$attr = array("member" => $ldap_license_dn);
		$logstr = "license=" . quote($licenseid);
		acc_log('license_revoke', $adm, $login, $logstr);

		if (!@ldap_mod_del($conn, $ldap_user_dn, $attr))
		{
			$errno = ldap_errno($conn);

			if ($errno == 0x10) /* LDAP_NO_SUCH_ATTRIBUTE */
				return 1;

			return 0;
		}

		return(1);
	}
	return(0);
}

function acc_usercreate($adm, $login, $password, $home, $name, $org, $orgtype, $cresident, $corigin, $sex, $disability, $hispanic, $race, $nativetribe, $email, $web, $phone, $reason, $mailPreferenceOption, $usageAgreement, $ip, $host, $jobs_allowed, $proxy = false, $uidNumber = 0) {
    global $hub;

    $conn =& XFactory::getPLDC();
    $xhub =& XFactory::getHub();
    
    $hubLDAPBaseDN = $xhub->getCfg('hubLDAPBaseDN');

    if (!$conn)
        return;

	$ldap_add_str = "uid=[login],ou=users," . $hub['ldap_base'];
	$gid_number = "3000";
	$login_shell = "/bin/bash";
	$ftp_shell = "/usr/lib/sftp-server";

	if (empty($uidNumber) || !is_numeric($uidNumber) || $uidNumber < 0)
	    $uid_number = acc_nextuid();
	else
	    $uid_number = $uidNumber;

	if (empty($home))
		$home = $xhub->getCfg('hubHomeDir') . DS . $login;

	if (empty($name))
		$name = $login;

	if (empty($email))
	    $email = '';
	
	if (empty($name))
	    $name = $login;

	if($login && /*$password && $home && $name && $email &&*/ $uid_number)
	{
		$ldap_add_dn = str_replace("[login]", $login, $ldap_add_str);
		$reg_date = date('Y-m-d H:i:s');
		$attr = array();
		$attr["objectclass"] = array();
		$attr["objectclass"][0] = "top";
		$attr["objectclass"][1] = "person";
		$attr["objectclass"][2] = "organizationalPerson";
		$attr["objectclass"][3] = "inetOrgPerson";
		$attr["objectclass"][4] = "posixAccount";
		$attr["objectclass"][5] = "shadowAccount";
		$attr["objectclass"][6] = "hubAccount";
		$attr["uid"] = $login;
		if (!empty($password))
			$attr["userPassword"] = acc_encryptpassword($password);
		$attr["gid"] = "public";
		if($home) {
			$attr["homeDirectory"] = $home;
		}
		if($name) {
			$attr["cn"] = $name;
			$attr["sn"] = $name;
		}
		if($orgtype) {
			$attr["orgtype"] = $orgtype;
		}
		if($org) {
			$attr["o"] = $org;
		}
		if($cresident) {
			$attr["countryresident"] = $cresident;
		}
		if($corigin) {
			$attr["countryorigin"] = $corigin;
		}
		if($sex) {
			$attr["sex"] = $sex;
		}
		if($disability) {
			for($i = 0; $i < count($disability); $i++) {
				$attr["disability"][$i] = $disability[$i];
			}
		}
		if($hispanic) {
			for($i = 0; $i < count($hispanic); $i++) {
				$attr["hispanic"][$i] = $hispanic[$i];
			}
		}
		if($race) {
			for($i = 0; $i < count($race); $i++) {
				$attr["race"][$i] = $race[$i];
			}
		}
		if($nativetribe) {
			$attr["nativeTribe"] = $nativetribe;
		}
		if($email) {
			$attr["mail"] = $email;
		}
		if($web) {
			$attr["url"] = $web;
		}
		if($phone) {
			$attr["homePhone"] = $phone;
		}
		if($reason) {
			$attr["description"] = $reason;
		}
		if($mailPreferenceOption) {
			$attr["mailPreferenceOption"] = 1;
		} else {
			$attr["mailPreferenceOption"] = 0;
		}
		if($usageAgreement) {
			$attr["usageAgreement"] = "TRUE";
		}
		else {
			$attr["usageAgreement"] = "FALSE";
		}
		if($ip) {
			$attr["regIp"] = $ip;
		}
		if($host) {
			$attr["regHost"] = $host;
		}
		$attr["regDate"] = $reg_date;
		if($jobs_allowed) {
			$attr["jobsAllowed"] = $jobs_allowed;
		}
		$attr["uidNumber"] = $uid_number;
		$attr["gidNumber"] = $gid_number;
		$attr["loginShell"] = $login_shell;
		$attr["ftpShell"] = $ftp_shell;
		$attr["member"] = "license=public,ou=licenses," . $hubLDAPBaseDN;
		if($proxy) {
		    ximport('xuser');
			$admxuser =& XUser::getInstance($adm);
			if(empty($admxuser)) {
				return(false);
			}
			$attr["proxyUidNumber"] = $admxuser->get('uid');
			if (!empty($password))
				$attr["proxyPassword"] = $password;
		}
		if(@ldap_add($conn, $ldap_add_dn, $attr)) {
			$keys = array_keys($attr);
			$logstr = "";
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
			acc_log('user_create', $adm, $login, $logstr);
			if(strtoupper($cresident) == "US") {
				acc_userlicense($login, $login, "us");
			}
			return(true);
		}
	}
	return(false);
}

function acc_nextuid() {
    global $hub;

    $conn =& XFactory::getPLDC();

    if (!$conn)
        return;

	$ldap_dn = "cn=maxuid," . $hub['ldap_base'];
	$max_tries = 5;

	$tries = 0;
	$nextuid = 0;
	while(!$nextuid && $tries < $max_tries) {
		$tries++;
		$ldap_search_str = "(uidNumber=*)";
		$reqattr = array("uidNumber");
		$uidentry = ldap_search($conn, $ldap_dn, $ldap_search_str, $reqattr, 0, 0, 0, 3);
		$entry = ldap_first_entry($conn, $uidentry);
		if($entry) {
			$attr = ldap_get_attributes($conn, $entry);
			$nextuid = $attr[$reqattr[0]][0];
			if($nextuid) {
				$attr = array("lock" => $nextuid);
				$mod = ldap_mod_add($conn, $ldap_dn, $attr);
				if($mod) {
					$ldap_search_str = "(lock=" . $nextuid . ")";
					$reqattr = array("uidNumber");
					$uidentry = ldap_search($conn, $ldap_dn, $ldap_search_str, $reqattr, 0, 0, 0, 3);
					$entry = ldap_first_entry($conn, $uidentry);
					if($entry) {
						$attr = ldap_get_attributes($conn, $entry);
						if($nextuid == $attr[$reqattr[0]][0]) {
							$nextuid++;
							$attr = array("uidNumber" => $nextuid);
							if(!ldap_mod_replace($conn, $ldap_dn, $attr)) {
								$nextuid = 0;
							}
							$attr = array("lock" => array());
							ldap_mod_del($conn, $ldap_dn, $attr);
						}
					}
					else {
						$nextuid = 0;
						sleep(2);
					}
				}
				else {
					$nextuid = 0;
					sleep(2);
				}
			}
		}
	}
	return($nextuid);
}

function acc_userdelete($adm, $login, $dtmodify) {
    global $hub;

    $conn =& XFactory::getPLDC();

    if (!$conn)
        return;

	$ldap_delete_str = "uid=[login],ou=users," . $hub['ldap_base'];

	if($conn && $login) {
		$ldap_delete_dn = str_replace("[login]", $login, $ldap_delete_str);
		if(ldap_delete($conn, $ldap_delete_dn)) {
			acc_log('user_delete', $adm, $login, "");
			return(true);
		}
	}
	return(false);
}

function acc_userupdate($adm, $login, $home, $name, $org, $orgtype, $cresident, $corigin, $sex, $disability, $hispanic, $race, $nativetribe, $email, $web, $phone, $reason, $mailPreferenceOption, $usageAgreement, $jobs_allowed, $dtmodify) {
    global $hub;

    $conn =& XFactory::getPLDC();

    if (!$conn)
        return;

	$ldap_mod_str = "uid=[login],ou=users," . $hub['ldap_base'];
	if($conn && $login) {
		ximport('xuser');
		$xuser =& XUser::getInstance($login);
		$ldap_mod_dn = str_replace("[login]", $login, $ldap_mod_str);
		$attr = array();
		$delattr = array();
		if($home) {
			$attr["homeDirectory"] = $home;
		}
		if($name) {
			$attr["cn"] = $name;
			$attr["sn"] = $name;
		}
		if($orgtype) {
			$attr["orgtype"] = $orgtype;
		}
		elseif($xuser->get('orgtype')) {
			$delattr["orgtype"] = array();
		}
		if($org) {
			$attr["o"] = $org;
		}
		elseif($xuser->get('org')) {
			$delattr["o"] = array();
		}
		if($cresident) {
			$attr["countryresident"] = $cresident;
		}
		elseif($xuser->get('countryresident')) {
			$delattr["countryresident"] = array();
		}
		if($corigin) {
			$attr["countryorigin"] = $corigin;
		}
		elseif($xuser->get('countryorigin')) {
			$delattr["countryorigin"] = array();
		}
		if($sex) {
			$attr["sex"] = $sex;
		}
		elseif($xuser->get('sex')) {
			$delattr["sex"] = array();
		}
		if($disability) {
			for($i = 0; $i < count($disability); $i++) {
				$attr["disability"][$i] = $disability[$i];
			}
		}
		elseif($xuser->get('disability')) {
			$delattr["disability"] = array();
		}
		if($hispanic) {
			for($i = 0; $i < count($hispanic); $i++) {
				$attr["hispanic"][$i] = $hispanic[$i];
			}
		}
		elseif($xuser->get('hispanic')) {
			$delattr["hispanic"] = array();
		}
		if($race) {
			for($i = 0; $i < count($race); $i++) {
				$attr["race"][$i] = $race[$i];
			}
		}
		elseif($xuser->get('race')) {
			$delattr["race"] = array();
		}
		if($nativetribe) {
			$attr["nativeTribe"] = $nativetribe;
		}
		elseif($xuser->get('nativetribe')) {
			$delattr["nativeTribe"] = array();
		}
		if($email) {
			$attr["mail"] = $email;
		}
		if($web) {
			$attr["url"] = $web;
		}
		elseif($xuser->get('web')) {
			$delattr["url"] = array();
		}
		if($phone) {
			$attr["homePhone"] = $phone;
		}
		elseif($xuser->get('phone')) {
			$delattr["homePhone"] = array();
		}
		if($reason) {
			$attr["description"] = $reason;
		}
		elseif($xuser->get('reason')) {
			$delattr["description"] = array();
		}
		if($mailPreferenceOption) {
			$attr["mailPreferenceOption"] = 1;
		} else {
			$attr["mailPreferenceOption"] = 0;
		}
		if($usageAgreement) {
			$attr["usageAgreement"] = "TRUE";
		}
		else {
			$attr["usageAgreement"] = "FALSE";
		}
		if($jobs_allowed) {
			$attr["jobsAllowed"] = $jobs_allowed;
		}
		if($dtmodify) {
			$attr["modDate"] = $dtmodify;
		}
		if(acc_getuserproxypassword($login)) {
			$delattr["proxyPassword"] = array();
		}
		if(ldap_mod_replace($conn, $ldap_mod_dn, $attr)) {
			$keys = array_keys($attr);
			$logstr = "";
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
			acc_log('user_update', $adm, $login, $logstr);
			if(strtoupper($cresident) == "US") {
				acc_userlicense($login, $login, "us");
			}
			else {
				acc_userdelicense($login, $login, "us");
			}
			if(ldap_mod_del($conn, $ldap_mod_dn, $delattr)) {
				return(1);
			}
		}
	}
	return(0);
}

function acc_getuserproxypassword($login) {
    global $hub;

    $conn =& XFactory::getLDC();

    if (!$conn)
        return;

	$ldap_base_dn = "ou=users,".$hub['ldap_base'];
	$ldap_search_str = "(uid=[login])";

	$ldap_search_str = str_replace("[login]", $login, $ldap_search_str);
	$reqattr = array();
	array_push($reqattr, "proxyPassword");
	$userentry = ldap_search($conn, $ldap_base_dn, $ldap_search_str, $reqattr, 0, 0, 0, 3);
	$entry = ldap_first_entry($conn, $userentry);
	if($entry) {
		$attr = ldap_get_attributes($conn, $entry);
		$proxypassword = isset($attr[$reqattr[0]][0]) ? $attr[$reqattr[0]][0] : false;
		return($proxypassword);
	}

	return(null);
}

function acc_useractivate($adm, $login) {
    global $hub;

    $conn =& XFactory::getPLDC();

    if (!$conn || !$login)
        return(0);

	$ldap_mod_str = "uid=[login],ou=users," . $hub['ldap_base'];
	$ldap_mod_dn = str_replace("[login]", $login, $ldap_mod_str);
	$attr = array("active" => "TRUE");

	acc_log('user_activate', $adm, $login, "");

	if (ldap_mod_replace($conn, $ldap_mod_dn, $attr))
		return(1);

	return(ldap_mod_add($conn, $ldap_mod_dn, $attr));
}

function acc_userdeactivate($adm, $login) {
    global $hub;

    $conn =& XFactory::getPLDC();

    if (!$conn || !$login)
        return(0);

	$ldap_mod_str = "uid=[login],ou=users," . $hub['ldap_base'];

	$ldap_mod_dn = str_replace("[login]", $login, $ldap_mod_str);
	$attr = array("active" => "FALSE");

	acc_log('user_deactivate', $adm, $login, "");

	if(ldap_mod_replace($conn, $ldap_mod_dn, $attr))
		return(1);

	return(ldap_mod_add($conn, $ldap_mod_dn, $attr));
}

function acc_useremailconfirm($adm, $login) {
    global $hub;

    $conn =& XFactory::getPLDC();

    if (!$conn || !$login)
        return(0);

	$ldap_mod_str = "uid=[login],ou=users," . $hub['ldap_base'];
	$ldap_mod_dn = str_replace("[login]", $login, $ldap_mod_str);
	$attr = array("emailConfirmed" => 1);

	acc_log('email_confirm', $adm, $login, "");
	
	if (ldap_mod_replace($conn, $ldap_mod_dn, $attr)) 
		return(1);

	return(ldap_mod_add($conn, $ldap_mod_dn, $attr));
}

function acc_useremailunconfirm($adm, $login) {
    global $hub;

    $conn =& XFactory::getPLDC();

    if (!$conn || !$login)
        return(0);

	$ldap_mod_str = "uid=[login],ou=users," . $hub['ldap_base'];

	$ldap_mod_dn = str_replace("[login]", $login, $ldap_mod_str);
	$emailconfirmed = acc_genemailconfirm();
	$attr = array("emailConfirmed" => $emailconfirmed);
	$logstr = "emailConfirmed=" . quote($emailconfirmed);

	acc_log('email_unconfirm', $adm, $login, $logstr);

	if (ldap_mod_replace($conn, $ldap_mod_dn, $attr))
		return($emailconfirmed);

	if(ldap_mod_add($conn, $ldap_mod_dn, $attr))
		return($emailconfirmed);
	
	return(0);
}

function acc_useredulevelset($adm, $login, $edulevel) {
    global $hub;

    $conn =& XFactory::getPLDC();

    if (!$conn || !$login || !$edulevel)
        return(0);

	$ldap_mod_str = "uid=[login],ou=users," . $hub['ldap_base'];

	$ldap_mod_dn = str_replace("[login]", $login, $ldap_mod_str);
	$attr = array("edulevel" => $edulevel);
	$logstr = "edulevel=" . quote($edulevel);
	
	acc_log('user_edulevel_add', $adm, $login, $logstr);
	
	return(ldap_mod_add($conn, $ldap_mod_dn, $attr));
}

function acc_useredulevelunset($adm, $login, $edulevel) {
    global $hub;

    $conn =& XFactory::getPLDC();

    if (!$conn || !$login || !$edulevel)
        return(0);

	$ldap_mod_str = "uid=[login],ou=users," . $hub['ldap_base'];

	$ldap_mod_dn = str_replace("[login]", $login, $ldap_mod_str);
	$attr = array("edulevel" => $edulevel);
	$logstr = "edulevel=" . quote($edulevel);
	
	acc_log('user_edulevel_remove', $adm, $login, $logstr);
	
	return(ldap_mod_del($conn, $ldap_mod_dn, $attr));
}

function acc_userroleset($adm, $login, $role) {
    global $hub;

    $conn =& XFactory::getPLDC();

    if (!$conn || !$login || !$role)
        return(0);

	$ldap_mod_str = "uid=[login],ou=users," . $hub['ldap_base'];

	$ldap_mod_dn = str_replace("[login]", $login, $ldap_mod_str);
	$attr = array("role" => $role);
	$logstr = "role=" . quote($role);
	
	acc_log('user_role_add', $adm, $login, $logstr);
	
	return(ldap_mod_add($conn, $ldap_mod_dn, $attr));
}

function acc_userroleunset($adm, $login, $role) {
    global $hub;

    $conn =& XFactory::getPLDC();

    if (!$conn || !$login || !$role)
        return(0);

	$ldap_mod_str = "uid=[login],ou=users," . $hub['ldap_base'];

	$ldap_mod_dn = str_replace("[login]", $login, $ldap_mod_str);
	$attr = array("role" => $role);
	$logstr = "role=" . quote($role);
	
	acc_log('user_role_remove', $adm, $login, $logstr);

	return(ldap_mod_del($conn, $ldap_mod_dn, $attr));
}

function acc_setuserpassword($adm, $login, $password) {
    global $hub;

    $conn =& XFactory::getPLDC();

    if (!$conn || !$login)
        return(0);

	$ldap_mod_str = "uid=[login],ou=users," . $hub['ldap_base'];

	$ldap_mod_dn = str_replace("[login]", $login, $ldap_mod_str);
	$attr = array();
	$attr["userPassword"] = acc_encryptpassword($password);
	
	acc_log('password_change', $adm, $login, "");
	
	if(ldap_mod_replace($conn, $ldap_mod_dn, $attr)) 
		return(1);
	
	return(ldap_mod_add($conn, $ldap_mod_dn, $attr));
}

function acc_getemailusers($email) {
    global $hub;

    $conn =& XFactory::getLDC();

    if (!$conn || !$email)
        return(0);

	$ldap_base_dn = "ou=users," . $hub['ldap_base'];
	$ldap_search_str = "(mail=[mail])";

	$getemailusers = array();
	$ldap_search_str = str_replace("[mail]", $email, $ldap_search_str);
	$reqattr = array();
	array_push($reqattr, "uid");
	$userentry = ldap_search($conn, $ldap_base_dn, $ldap_search_str, $reqattr, 0, 0, 0, 3);
	$entry = ldap_first_entry($conn, $userentry);
	while($entry) {
		$attr = ldap_get_attributes($conn, $entry);
		array_push($getemailusers, $attr[$reqattr[0]][0]);
		$entry = ldap_next_entry($conn, $entry);
	}

	return($getemailusers);
}

function acc_checkpassword($password) {
    global $hub;

	$juser =& JFactory::getUser();
    $conn = @ldap_connect($hub['ldap_host']);
    @ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);
    $result = @ldap_bind($conn, "uid=" . $juser->get('username') . ",ou=Users," .  $hub['ldap_base'], $password);
    @ldap_close($conn);

	return $result;
}

?>
