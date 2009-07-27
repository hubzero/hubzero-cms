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
ximport('account.acct_groups');


function acc_toolcreate($adm, $tool, $name, $public, $description, $exportControl, $defaultMiddleware, $middleware, $version, $revision, $state, $sourcePublic, $projectPublic, $priority, $author, $owner, $member, $publishDate = null, $unpublishDate = null, $vncCommand = null, $vncGeometry = null) {
	global $hub;

	$conn =& XFactory::getPLDC();
	if(!$conn || !$tool || !$name || $version === NULL || $revision === NULL) {
		return(null);
	}

	$ldap_add_str = "tool=[tool],ou=tools," . $hub['ldap_base'];
	$ldap_group_str = "gid=[gid],ou=groups," . $hub['ldap_base'];

	$ldap_add_dn = str_replace("[tool]", $tool, $ldap_add_str);
	$attr = array();
	$attr["objectclass"] = array();
	$attr["objectclass"][0] = "top";
	$attr["objectclass"][1] = "hubTool";
	$attr["tool"] = $tool;
	$attr["cn"] = $name;
	if($public) {
		$attr["public"] = "TRUE";
	}
	else {
		$attr["public"] = "FALSE";
	}
	if($description) {
		$attr["description"] = $description;
	}
	if($exportControl) {
		$attr["exportControl"] = $exportControl;
	}
	if($defaultMiddleware) {
		$attr["defaultMiddleware"] = $defaultMiddleware;
	}
	if($middleware) {
		$attr["middleware"] = $middleware;
	}
	$attr["version"] = $version;
	$attr["revision"] = $revision;
	if($state) {
		$attr["state"] = $state;
	}
	if($sourcePublic) {
		$attr["sourcePublic"] = "TRUE";
		if(!isset($attr["tracperm"])) {
			$attr["tracperm"] = array();
		}
		array_push($attr["tracperm"], "BROWSER_VIEW");
		array_push($attr["tracperm"], "LOG_VIEW");
		array_push($attr["tracperm"], "FILE_VIEW");
		array_push($attr["tracperm"], "CHANGESET_VIEW");
	}
	else {
		$attr["sourcePublic"] = "FALSE";
	}
	if($projectPublic) {
		$attr["projectPublic"] = "TRUE";
		if(!isset($attr["tracperm"])) {
			$attr["tracperm"] = array();
		}
		array_push($attr["tracperm"], "WIKI_VIEW");
		array_push($attr["tracperm"], "MILESTONE_VIEW");
		array_push($attr["tracperm"], "ROADMAP_VIEW");
		array_push($attr["tracperm"], "SEARCH_VIEW");
	}
	else {
		$attr["projectPublic"] = "FALSE";
	}
	if($priority) {
		$attr["priority"] = $priority;
	}
	if($author) {
		$attr["author"] = $author;
	}
	if($owner) {
		if(is_array($owner)) {
			$attr["owner"] = array();
			foreach($owner as $gid) {
				array_push($attr["owner"], str_replace("[gid]", $gid, $ldap_group_str));
			}
		}
		else {
			$attr["owner"] = str_replace("[gid]", $owner, $ldap_group_str);
		}
	}
	if($member) {
		if(is_array($member)) {
			$attr["member"] = array();
			foreach($member as $gid) {
				array_push($attr["member"], str_replace("[gid]", $gid, $ldap_group_str));
			}
		}
		else {
			$attr["member"] = str_replace("[gid]", $member, $ldap_group_str);
		}
	}
	if($publishDate) {
		$attr['publishDate'] = $publishDate;
	}
	if($unpublishDate) {
		$attr['unpublishDate'] = $unpublishDate;
	}
	if($vncCommand) {
		$attr['vncCommand'] = $vncCommand;
	}
	if($vncGeometry) {
		$attr['vncGeometry'] = $vncGeometry;
	}

	if(ldap_add($conn, $ldap_add_dn, $attr)) {
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

		acc_log('tool_create', $adm, $tool, $logstr);
		
		return($tool);
	}
	return(null);
}


function acc_toolnamecreate($adm, $toolname, $name, $member) {
	global $hub;

	$conn =& XFactory::getPLDC();
	if(!$conn || !$toolname || !$name) {
		return(null);
	}

	$ldap_add_str = "toolname=[toolname],ou=toolnames," . $hub['ldap_base'];
	$ldap_tool_str = "tool=[tool],ou=tools," . $hub['ldap_base'];

	$ldap_add_dn = str_replace("[toolname]", $toolname, $ldap_add_str);
	$attr = array();
	$attr["objectclass"] = array();
	$attr["objectclass"][0] = "top";
	$attr["objectclass"][1] = "hubToolName";
	$attr["toolName"] = $toolname;
	$attr["cn"] = $name;
	if($member) {
		if(is_array($member)) {
			$attr["member"] = array();
			foreach($member as $tool) {
				array_push($attr["member"], str_replace("[tool]", $tool, $ldap_tool_str));
			}
		}
		else {
			$attr["member"] = str_replace("[tool]", $member, $ldap_tool_str);
		}
	}

	if(ldap_add($conn, $ldap_add_dn, $attr)) {
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

		acc_log('toolname_create', $adm, $toolname, $logstr);
		
		return($toolname);
	}
	return(null);
}


function acc_toolupdate($adm, $tool, $name, $public, $description, $exportControl, $defaultMiddleware, $middleware, $version, $revision, $state, $sourcePublic, $projectPublic, $priority, $author, $owner = null, $member = null, $publishDate = null, $unpublishDate = null, $vncCommand = null, $vncGeometry = null) {
	global $hub;

	$conn =& XFactory::getPLDC();
	if(!$conn || !$tool || !$name || $version === NULL || $revision === NULL) {
		return(0);
	}

	$ldap_mod_str = "tool=[tool],ou=tools," . $hub['ldap_base'];
	$ldap_group_str = "gid=[gid],ou=groups," . $hub['ldap_base'];

	$ldap_mod_dn = str_replace("[tool]", $tool, $ldap_mod_str);
	$toolinfo = acc_gettool($tool);
	$attr = array();
	$delattr = array();
	$attr['cn'] = $name;
	if($public === true) {
		$attr['public'] = "TRUE";
	}
	elseif($public === false) {
		$attr['public'] = "FALSE";
	}
	if($description) {
		$attr['description'] = $description;
	}
	elseif($toolinfo['description']) {
		$delattr['description'] = array();
	}
	if($exportControl) {
		$attr['exportControl'] = $exportControl;
	}
	elseif($toolinfo['exportControl']) {
		$delattr['exportControl'] = array();
	}
	if($defaultMiddleware) {
		$attr['defaultMiddleware'] = $defaultMiddleware;
	}
	elseif($toolinfo['defaultMiddleware']) {
		$delattr['defaultMiddleware'] = array();
	}
	if($middleware) {
		$attr['middleware'] = $middleware;
	}
	elseif(count($toolinfo['middleware'])) {
		$delattr['middleware'] = array();
	}
	if($version) {
		$attr['version'] = $version;
	}
	elseif($toolinfo['version']) {
		$delattr['version'] = array();
	}
	if($revision) {
		$attr['revision'] = $revision;
	}
	elseif($toolinfo['revision']) {
		$delattr['revision'] = array();
	}
	if($state) {
		$attr['state'] = $state;
	}
	elseif($toolinfo['state']) {
		$delattr['state'] = array();
	}
	if($sourcePublic === true) {
		$attr['sourcePublic'] = "TRUE";
		if(!isset($attr["tracperm"])) {
			$attr["tracperm"] = array();
		}
		array_push($attr["tracperm"], "BROWSER_VIEW");
		array_push($attr["tracperm"], "LOG_VIEW");
		array_push($attr["tracperm"], "FILE_VIEW");
		array_push($attr["tracperm"], "CHANGESET_VIEW");
	}
	if($projectPublic === true) {
		$attr['projectPublic'] = "TRUE";
		if(!isset($attr["tracperm"])) {
			$attr["tracperm"] = array();
		}
		array_push($attr["tracperm"], "WIKI_VIEW");
		array_push($attr["tracperm"], "MILESTONE_VIEW");
		array_push($attr["tracperm"], "ROADMAP_VIEW");
		array_push($attr["tracperm"], "SEARCH_VIEW");
	}
	elseif($projectPublic === false) {
		$attr['projectPublic'] = "FALSE";
		if(!isset($attr["tracperm"])) {
			$delattr['tracperm'] = array();
		}
	}
	if($sourcePublic === false) {
		$attr['sourcePublic'] = "FALSE";
		if(!isset($attr["tracperm"])) {
			$delattr['tracperm'] = array();
		}
	}
	if($priority) {
		$attr['priority'] = $priority;
	}
	elseif($toolinfo['priority']) {
		$delattr['priority'] = array();
	}
	if($author) {
		$attr['author'] = $author;
	}
	elseif(count($toolinfo['author'])) {
		$delattr['author'] = array();
	}
	if($owner) {
		if(is_array($owner)) {
			$attr['owner'] = array();
			foreach($owner as $gid) {
				array_push($attr['owner'], str_replace("[gid]", $gid, $ldap_group_str));
			}
		}
		else {
			$attr['owner'] = str_replace("[gid]", $owner, $ldap_group_str);
		}
	}
	elseif(is_array($owner) && count($toolinfo['owner'])) {
		$delattr['owner'] = array();
	}
	if($member) {
		if(is_array($member)) {
			$attr['member'] = array();
			foreach($member as $gid) {
				array_push($attr['member'], str_replace("[gid]", $gid, $ldap_group_str));
			}
		}
		else {
			$attr['member'] = str_replace("[gid]", $owner, $ldap_group_str);
		}
	}
	elseif(is_array($member) && count($toolinfo['member'])) {
		$delattr['member'] = array();
	}
	if($publishDate) {
		$attr['publishDate'] = $publishDate;
	}
	elseif($publishDate === "") {
		$delattr['publishDate'] = array();
	}
	if($unpublishDate) {
		$attr['unpublishDate'] = $unpublishDate;
	}
	elseif($unpublishDate === "") {
		$delattr['unpublishDate'] = array();
	}
	if($vncCommand) {
		$attr['vncCommand'] = $vncCommand;
	}
	elseif($vncCommand === "") {
		$delattr['vncCommand'] = array();
	}
	if($vncGeometry) {
		$attr['vncGeometry'] = $vncGeometry;
	}
	elseif($vncGeometry === "") {
		$delattr['vncGeometry'] = array();
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
		acc_log('tool_update', $adm, $tool, $logstr);

		if(ldap_mod_del($conn, $ldap_mod_dn, $delattr)) {
			return(1);
		}
	}
	return(0);
}


function acc_toolnameupdate($adm, $toolname, $name, $member) {
	global $hub;

	$conn =& XFactory::getPLDC();
	if(!$conn || !$toolname || !$name) {
		return(0);
	}

	$ldap_mod_str = "toolname=[toolname],ou=toolnames," . $hub['ldap_base'];
	$ldap_tool_str = "tool=[tool],ou=tools," . $hub['ldap_base'];

	$ldap_mod_dn = str_replace("[toolname]", $toolname, $ldap_mod_str);
	$toolnameinfo = acc_gettoolname($toolname);
	$attr = array();
	$delattr = array();
	$attr["cn"] = $name;
	if($member) {
		if(is_array($member)) {
			$attr["member"] = array();
			foreach($member as $tool) {
				array_push($attr["member"], str_replace("[tool]", $tool, $ldap_tool_str));
			}
		}
		else {
			$attr["member"] = str_replace("[tool]", $owner, $ldap_tool_str);
		}
	}
	elseif(count($toolnameinfo['member'])) {
		$delattr["member"] = array();
	}
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

	if(ldap_mod_replace($conn, $ldap_mod_dn, $attr)) {
		acc_log('toolname_update', $adm, $toolname, $logstr);

		if(ldap_mod_del($conn, $ldap_mod_dn, $delattr)) {
			return(1);
		}
	}
	return(0);
}


function acc_gettoolaccess($tool, $login = '') {
	global $hub;

	$conn =& XFactory::getLDC();
	if(!$conn) {
		return("");
	}

	if ($login == '') {
		$juser =& JFactory::getUser();
		$login = $juser->get('username');
	}

	$ldap_base_str = "uid=[login],ou=users," . $hub['ldap_base'];

	if($login && $tool) {
		$licensed = false;
		$toolinfo = acc_gettool($tool);
		if(isset($toolinfo['owner']) && is_array($toolinfo['owner']) && isset($toolinfo['member']) && is_array($toolinfo['member'])) {
			$licgroups = array_merge($toolinfo['owner'], $toolinfo['member']);
			foreach($licgroups as $licgroup) {
				$groupinfo = acc_getgroup($licgroup);
				
				if(isset($groupinfo['owner']) && sizeof($groupinfo['owner']) != 0 && in_array($login, $groupinfo['owner']) || isset($groupinfo['member']) && sizeof($groupinfo['member']) != 0 && in_array($login, $groupinfo['member'])) {
					$licensed = true;
				}
			}
		}
		if(!$licensed) {
			$ldap_base_dn = str_replace("[login]", $login, $ldap_base_str);
			$reqattr = array("member");
			$userentry = ldap_search($conn, $ldap_base_dn, "(objectclass=*)", $reqattr, 0, 0, 0, 3);
			$count = ldap_count_entries($conn, $userentry);
			if($count > 0) {
				$firstentry = ldap_first_entry($conn, $userentry);
				$attr = ldap_get_attributes($conn, $firstentry);
				for($i = 0; $i < count($attr[$reqattr[0]]) - 1; $i++) {
					$ldap_base_dn = $attr[$reqattr[0]][$i];
					$reqattr2 = array("license");
					$ldap_search_str = "(member=tool=" . $tool . ",ou=tools," . $hub['ldap_base'] . ")";
					$licentry = ldap_search($conn, $ldap_base_dn, $ldap_search_str, $reqattr2, 0, 0, 0, 3);
					$count = ldap_count_entries($conn, $licentry);
					if($count > 0) {
						$licensed = true;
						$i = count($attr[$reqattr[0]]) - 1;
					}
				}
			}
		}
		if($licensed) {
			if($toolinfo['exportControl'] == "us") {
				if(ipcountry($_SERVER['REMOTE_ADDR']) == "us") {
					return("");
				}
				else {
					return("this tool may only be accessed from within the U.S.  Your current location could not be confirmed.");
				}
			}
			elseif($toolinfo['exportControl'] == "d1") {
				if(is_d1nation(ipcountry($_SERVER['REMOTE_ADDR']))) {
					return("this tool may not be accessed from your current location due to export restrictions.");
				}
				else {
					return("");
				}
			}
			elseif($toolinfo['exportControl'] == "pu") {
				if(is_iplocation($_SERVER['REMOTE_ADDR'], $toolinfo['exportControl'])) {
					return("");
				}
				else {
					return("this tool may only be accessed by authorized users while on the West Lafayette campus of Purdue University due to license restrictions.");
				}
			}
			else {
				return("");
			}
		}
	}
	return("you are not currently authorized to use this tool.");
}


function acc_gettools($alltools = null, $reqmiddleware = null) {
	global $hub;

	$conn =& XFactory::getLDC();
	if(!$conn) {
		return('');
	}

	$login = null;
	if($alltools !== true) {
		$juser =& JFactory::getUser();
		$login = $juser->get('username');
	}
	if($reqmiddleware && !is_array($reqmiddleware)) {
		$reqmiddleware = array($reqmiddleware);
	}

	$ldap_base_dn = "ou=tools," . $hub['ldap_base'];

	$tools = array();
	$reqattr = array();
	$reqattr[] = 'tool';
	$reqattr[] = 'cn';
	$reqattr[] = 'public';
	$reqattr[] = 'description';
	$reqattr[] = 'exportControl';
	$reqattr[] = 'defaultMiddleware';
	$reqattr[] = 'middleware';
	$reqattr[] = 'version';
	$reqattr[] = 'revision';
	$reqattr[] = 'state';
	$reqattr[] = 'sourcePublic';
	$reqattr[] = 'projectPublic';
	$reqattr[] = 'priority';
	$reqattr[] = 'author';
	$reqattr[] = 'owner';
	$reqattr[] = 'member';
	$reqattr[] = 'publishDate';
	$reqattr[] = 'unpublishDate';
	$toolentries = ldap_search($conn, $ldap_base_dn, "(objectclass=*)", $reqattr, 0, 0, 0, 3);
	$entry = ldap_first_entry($conn, $toolentries);
	while($entry) {
		$attr = ldap_get_attributes($conn, $entry);
		if(isset($attr[$reqattr[0]][0])) {
			if($reqmiddleware) {
				$okmiddleware = false;
			}
			else {
				$okmiddleware = true;
			}
			$middleware = array();
			for($i = 0; $i < count($attr[$reqattr[6]]) - 1; $i++) {
				array_push($middleware, $attr[$reqattr[6]][$i]);
				if($reqmiddleware) {
					if(in_array($attr[$reqattr[6]][$i], $reqmiddleware)) {
						$okmiddleware = true;
					}
				}
			}
			if($okmiddleware) {
				$pushtool = false;
				if((isset($attr[$reqattr[2]][0]) && $attr[$reqattr[2]][0] == "TRUE") || $alltools === true) {
					$pushtool = true;
				}
				elseif($login) {
					if(!acc_gettoolaccess($attr[$reqattr[0]][0], $login)) {
						$pushtool = true;
					}
				}
				if($pushtool) {
					$toolinfo = array();
					if(isset($attr[$reqattr[0]][0])) {
						$toolinfo['tool'] = $attr[$reqattr[0]][0];
					} else { $toolinfo['tool'] = NULL; }
					if(isset($attr[$reqattr[1]][0])) {
						$toolinfo['name'] = $attr[$reqattr[1]][0];
					} else { $toolinfo['name'] = NULL; }
					if(isset($attr[$reqattr[2]][0])) {
						if($attr[$reqattr[2]][0] == "TRUE") {
							$toolinfo['public'] = true;
						}
						else {
							$toolinfo['public'] = false;
						}
					}
					else {
						$toolinfo['public'] = false;
					}
					if(isset($attr[$reqattr[3]][0])) {
						$toolinfo['description'] = $attr[$reqattr[3]][0];
					} else { $toolinfo['description'] = NULL; }
					if(isset($attr[$reqattr[4]][0])) {
						$toolinfo['exportControl'] = $attr[$reqattr[4]][0];
					} else { $toolinfo['exportControl'] = NULL; }
					if(isset($attr[$reqattr[5]][0])) {
						$toolinfo['defaultMiddleware'] = $attr[$reqattr[5]][0];
					} else { $toolinfo['defaultMiddleware'] = NULL; }
					if(isset($attr[$reqattr[6]][0])) {
						$toolinfo['middleware'] = array();
						for($i = 0; $i < count($attr[$reqattr[6]]); $i++) {
							if (!empty($attr[$reqattr[6]][$i]) && ($attr[$reqattr[6]][$i])) {
								array_push($toolinfo['middleware'], $attr[$reqattr[6]][$i]);
							}
						}
					}
					if(isset($attr[$reqattr[7]][0])) {
						$toolinfo['version'] = $attr[$reqattr[7]][0];
					} else { $toolinfo['version'] = NULL; }
					if(isset($attr[$reqattr[8]][0])) {
						$toolinfo['revision'] = $attr[$reqattr[8]][0];
					} else { $toolinfo['revision'] = NULL; }
					if(isset($attr[$reqattr[9]][0])) {
						$toolinfo['state'] = $attr[$reqattr[9]][0];
					} else { $toolinfo['state'] = NULL; }
					if(isset($attr[$reqattr[10]][0])) {
						if($attr[$reqattr[10]][0] == "TRUE") {
							$toolinfo['sourcePublic'] = true;
						}
						else {
							$toolinfo['sourcePublic'] = false;
						}
					}
					else {
						$toolinfo['sourcePublic'] = false;
					}
					if(isset($attr[$reqattr[11]][0])) {
						if($attr[$reqattr[11]][0] == "TRUE") {
							$toolinfo['projectPublic'] = true;
						}
						else {
							$toolinfo['projectPublic'] = false;
						}
					}
					else {
						$toolinfo['projectPublic'] = false;
					}
					if(isset($attr[$reqattr[12]][0])) {
						$toolinfo['priority'] = $attr[$reqattr[12]][0];
					} else { $toolinfo['priority'] = NULL; }
					$toolinfo['author'] = array();
					if(isset($attr[$reqattr[13]][0])) {
						
						for($i = 0; $i < count($attr[$reqattr[13]]); $i++) {
							if($attr[$reqattr[13]][$i]) {
								array_push($toolinfo['author'], $attr[$reqattr[13]][$i]);
							}
						}
					}
					$toolinfo['owner'] = array();
					if(isset($attr[$reqattr[14]][0])) {
						
						for($i = 0; $i < count($attr[$reqattr[14]]); $i++) {
							if($attr[$reqattr[14]][$i]) {
								$id = acc_extractfromdn($attr[$reqattr[14]][$i]);
								array_push($toolinfo['owner'], $id);
							}
						}
					}
					$toolinfo['member'] = array();
					if(isset($attr[$reqattr[15]][0])) {
						
						for($i = 0; $i < count($attr[$reqattr[15]]); $i++) {
							if (!empty($attr[$reqattr[15]][$i]) && ($attr[$reqattr[15]][$i])) {
								$id = acc_extractfromdn($attr[$reqattr[15]][$i]);
								array_push($toolinfo['member'], $id);
							}
						}
					}
					if(isset($attr[$reqattr[16]][0])) {
						$toolinfo['publishDate'] = $attr[$reqattr[16]][0];
					} else { $toolinfo['publishDate'] = NULL; }
					if(isset($attr[$reqattr[17]][0])) {
						$toolinfo['unpublishDate'] = $attr[$reqattr[17]][0];
					} else { $toolinfo['unpublishDate'] = NULL; }
					array_push($tools, $toolinfo);
				}
			}
		}
		$entry = ldap_next_entry($conn, $entry);
	}
	arraykeyedsort($tools, array('name'));
	return($tools);
}


function acc_gettoolnames() {
	global $hub;

	$conn =& XFactory::getLDC();
	if(!$conn) {
		return('');
	}

	$ldap_base_dn = "ou=toolnames," . $hub['ldap_base'];

	$toolnames = array();
	$reqattr = array();
	$reqattr[] = 'toolName';
	$reqattr[] = 'cn';
	$reqattr[] = 'member';
	$toolnameentries = ldap_search($conn, $ldap_base_dn, "(objectclass=*)", $reqattr, 0, 0, 0, 3);
	$entry = ldap_first_entry($conn, $toolnameentries);
	while($entry) {
		$attr = ldap_get_attributes($conn, $entry);
		if(isset($attr[$reqattr[0]][0])) {
			$toolnameinfo = array();
			if(isset($attr[$reqattr[0]][0])) {
				$toolnameinfo['toolname'] = $attr[$reqattr[0]][0];
			}
			if(isset($attr[$reqattr[1]][0])) {
				$toolnameinfo['name'] = $attr[$reqattr[1]][0];
			}
			if(isset($attr[$reqattr[2]][0])) {
				$toolnameinfo['member'] = array();
				for($i = 0; $i < count($attr[$reqattr[2]]); $i++) {
					if(isset($attr[$reqattr[2]][$i])) {
						$id = acc_extractfromdn($attr[$reqattr[2]][$i]);
						array_push($toolnameinfo['member'], $id);
					}
				}
			}
			array_push($toolnames, $toolnameinfo);
		}
		$entry = ldap_next_entry($conn, $entry);
	}
	arraykeyedsort($toolnames, array("name"));
	return($toolnames);
}


function acc_gettoolgroups() {
	global $hub;

	$conn =& XFactory::getLDC();
	if(!$conn) {
		return('');
	}

	$ldap_base_dn = "ou=toolgroups," . $hub['ldap_base'];

	$toolgroups = array();
	$reqattr = array('toolgroup', 'cn', 'member');
	$toolgroupentries = ldap_search($conn, $ldap_base_dn, "(objectclass=*)", $reqattr, 0, 0, 0, 3);
	$entry = ldap_first_entry($conn, $toolgroupentries);
	while($entry) {
		$attr = ldap_get_attributes($conn, $entry);
		if(isset($attr[$reqattr[0]][0]) && $attr[$reqattr[0]][0]) {
			$toolgroup = array(
			   'id' => $attr[$reqattr[0]][0],
			   'name' => $attr[$reqattr[1]][0],
			   'member' => array() );
			for($i = 0; $i < count($attr[$reqattr[2]]) - 1; $i++) {
				array_push($toolgroup['member'], $attr[$reqattr[2]][$i]);
			}
			array_push($toolgroups, $toolgroup);
		}
		$entry = ldap_next_entry($conn, $entry);
	}
	arraykeyedsort($toolgroups, array('name'));
	return($toolgroups);
}


function acc_gettoolalias($tool) {
	global $hub;

	$conn =& XFactory::getLDC();
	if(!$conn) {
		return('');
	}

	$ldap_base_dn = "ou=tools," . $hub['ldap_base'];

	$alias = array();
	$reqattr = array('alias');
	$toolentry = ldap_search($conn, $ldap_base_dn, "(tool=" . $tool . ")", $reqattr, 0, 0, 0, 3);
	$count = ldap_count_entries($conn, $toolentry);
	if($count > 0) {
		$firstentry = ldap_first_entry($conn, $toolentry);
		$attr = ldap_get_attributes($conn, $firstentry);
		for($i = 0; $i < count($attr[$reqattr[0]]) - 1; $i++) {
			array_push($alias, $attr[$reqattr[0]][$i]);
		}
	}
	return($alias);
}


function acc_gettoolgrouptools($toolgroup) {
	global $hub;

	$conn =& XFactory::getLDC();
	if(!$conn) {
		return('');
	}

	$ldap_base_dn = "ou=toolgroups," . $hub['ldap_base'];

	$tools = array();
	$reqattr = array('member');
	$toolgroupentry = ldap_search($conn, $ldap_base_dn, "(toolgroup=" . $toolgroup . ")", $reqattr, 0, 0, 0, 3);
	$count = ldap_count_entries($conn, $toolgroupentry);
	if($count > 0) {
		$firstentry = ldap_first_entry($conn, $toolgroupentry);
		$attr = ldap_get_attributes($conn, $firstentry);
		for($i = 0; $i < count($attr[$reqattr[0]]) - 1; $i++) {
			$tooldn = $attr[$reqattr[0]][$i];
			array_push($tools, substr($tooldn, 5, strpos($tooldn, ",") - 5));
		}
	}
	return($tools);
}


function acc_gettool($tool) {
	global $hub;

	$conn =& XFactory::getLDC();
	if(!$conn || !$tool) {
		return(array());
	}

	$ldap_base_dn = "ou=tools," . $hub['ldap_base'];

	$toolinfo = array();
	$reqattr = array();
	$reqattr[] = 'tool';
	$reqattr[] = 'cn';
	$reqattr[] = 'public';
	$reqattr[] = 'description';
	$reqattr[] = 'exportControl';
	$reqattr[] = 'defaultMiddleware';
	$reqattr[] = 'middleware';
	$reqattr[] = 'version';
	$reqattr[] = 'revision';
	$reqattr[] = 'state';
	$reqattr[] = 'sourcePublic';
	$reqattr[] = 'projectPublic';
	$reqattr[] = 'priority';
	$reqattr[] = 'author';
	$reqattr[] = 'owner';
	$reqattr[] = 'member';
	$reqattr[] = 'publishDate';
	$reqattr[] = 'unpublishDate';
	$reqattr[] = 'vncCommand';
	$reqattr[] = 'vncGeometry';
	$toolentry = ldap_search($conn, $ldap_base_dn, "(tool=" . $tool . ")", $reqattr, 0, 0, 0, 3);
	$count = ldap_count_entries($conn, $toolentry);
	if($count > 0) {
		$firstentry = ldap_first_entry($conn, $toolentry);
		$attr = ldap_get_attributes($conn, $firstentry);
		if(isset($attr[$reqattr[0]][0])) {
			$toolinfo['tool'] = $attr[$reqattr[0]][0];
		} else { $toolinfo['tool'] = NULL; }
		if(isset($attr[$reqattr[1]][0])) {
			$toolinfo['name'] = $attr[$reqattr[1]][0];
		} else { $toolinfo['name'] = NULL; }
		if(isset($attr[$reqattr[2]][0])) {
			if($attr[$reqattr[2]][0] == "TRUE") {
				$toolinfo['public'] = true;
			}
			else {
				$toolinfo['public'] = false;
			}
		}
		else {
			$toolinfo['public'] = false;
		}
		if(isset($attr[$reqattr[3]][0])) {
			$toolinfo['description'] = $attr[$reqattr[3]][0];
		} else { $toolinfo['description'] = NULL; }
		if(isset($attr[$reqattr[4]][0])) {
			$toolinfo['exportControl'] = $attr[$reqattr[4]][0];
		} else { $toolinfo['exportControl'] = NULL; }
		if(isset($attr[$reqattr[5]][0])) {
			$toolinfo['defaultMiddleware'] = $attr[$reqattr[5]][0];
		} else { $toolinfo['defaultMiddleware'] = NULL; }
		$toolinfo['middleware'] = array();
		if(isset($attr[$reqattr[6]][0])) {
			
			for($i = 0; $i < count($attr[$reqattr[6]]); $i++) {
				if(isset($attr[$reqattr[6]][$i])) {
					array_push($toolinfo['middleware'], $attr[$reqattr[6]][$i]);
				}
			}
		}
		if(isset($attr[$reqattr[7]][0])) {
			$toolinfo['version'] = $attr[$reqattr[7]][0];
		} else { $toolinfo['version'] = NULL; }
		if(isset($attr[$reqattr[8]][0])) {
			$toolinfo['revision'] = $attr[$reqattr[8]][0];
		} else { $toolinfo['revision'] = NULL; }
		if(isset($attr[$reqattr[9]][0])) {
			$toolinfo['state'] = $attr[$reqattr[9]][0];
		} else { $toolinfo['state'] = NULL; }
		if(isset($attr[$reqattr[10]][0])) {
			if($attr[$reqattr[10]][0] == "TRUE") {
				$toolinfo['sourcePublic'] = true;
			}
			else {
				$toolinfo['sourcePublic'] = false;
			}
		}
		else {
			$toolinfo['sourcePublic'] = false;
		}
		if(isset($attr[$reqattr[11]][0])) {
			if($attr[$reqattr[11]][0] == "TRUE") {
				$toolinfo['projectPublic'] = true;
			}
			else {
				$toolinfo['projectPublic'] = false;
			}
		}
		else {
			$toolinfo['projectPublic'] = false;
		}
		if(isset($attr[$reqattr[12]][0])) {
			$toolinfo['priority'] = $attr[$reqattr[12]][0];
		} else { $toolinfo['priority'] = NULL; }
		$toolinfo['author'] = array();
		if(isset($attr[$reqattr[13]][0])) {
			
			for($i = 0; $i < count($attr[$reqattr[13]]); $i++) {
				if(isset($attr[$reqattr[13]][$i])) {
					array_push($toolinfo['author'], $attr[$reqattr[13]][$i]);
				}
			}
		}
		$toolinfo['owner'] = array();
		if(isset($attr[$reqattr[14]][0])) {
			
			for($i = 0; $i < count($attr[$reqattr[14]]); $i++) {
				if(isset($attr[$reqattr[14]][$i])) {
					$id = acc_extractfromdn($attr[$reqattr[14]][$i]);
					array_push($toolinfo['owner'], $id);
				}
			}
		}
		$toolinfo['member'] = array();
		if(isset($attr[$reqattr[15]][0])) {
			
			for($i = 0; $i < count($attr[$reqattr[15]]); $i++) {
				if(isset($attr[$reqattr[15]][$i])) {
					$id = acc_extractfromdn($attr[$reqattr[15]][$i]);
					array_push($toolinfo['member'], $id);
				}
			}
		}
		if(isset($attr[$reqattr[16]][0])) {
			$toolinfo['publishDate'] = $attr[$reqattr[16]][0];
		} else { $toolinfo['publishDate'] = NULL; }
		if(isset($attr[$reqattr[17]][0])) {
			$toolinfo['unpublishDate'] = $attr[$reqattr[17]][0];
		} else { $toolinfo['unpublishDate'] = NULL; }
		if(isset($attr[$reqattr[18]][0])) {
			$toolinfo['vncCommand'] = $attr[$reqattr[18]][0];
		} else { $toolinfo['vncCommand'] = NULL; }
		if(isset($attr[$reqattr[19]][0])) {
			$toolinfo['vncGeometry'] = $attr[$reqattr[19]][0];
		} else { $toolinfo['vncGeometry'] = NULL; }
	}
	return($toolinfo);
}


function acc_gettoolnamestool($alltools = null, $reqmiddleware = null, $published = true) {
	global $hub;

	$conn =& XFactory::getLDC();
	if(!$conn) {
		return(array());
	}

	$login = null;
	if($alltools !== true) {
		$juser =& JFactory::getUser();
		$login = $juser->get('username');
	}
	if($reqmiddleware && !is_array($reqmiddleware)) {
		$reqmiddleware = array($reqmiddleware);
	}

	$toolsinfo = array();
	$toolnames = acc_gettoolnames();
	if($toolnames) {
		for($j = 0; $j < count($toolnames); $j++) {
			$tools = acc_gettoolnametools($toolnames[$j]['toolname'], $alltools);
			if($tools) {
				$toolinfo = array();
				for($i = 0; $i < count($tools); $i++) {
					if(!$published || strtolower($tools[$i]['state']) == 'published') {
						if(!$reqmiddleware) {
							$middlewareok = true;
						}
						else {
							$middlewareok = false;
							foreach($reqmiddleware as $possiblemiddleware) {
								if(in_array($possiblemiddleware, $tools[$i]['middleware'])) {
									$middlewareok = true;
								}
							}
						}
						if($middlewareok) {
							$pushtool = false;
							if($tools[$i]['public'] || $alltools === true) {
								$pushtool = true;
							}
							elseif($login) {
								if(!acc_gettoolaccess($tools[$i]['tool'], $login)) {
									$pushtool = true;
								}
							}
							if($pushtool) {
								$toolinfo = $tools[$i];
								$i = count($tools) + 1;
							}
						}
					}
				}
				if($toolinfo) {
					array_push($toolsinfo, $toolinfo);
				}
			}
		}
	}
	return($toolsinfo);
}


function acc_gettoolnametool($toolname, $published = true) {
	global $hub;

	$conn =& XFactory::getLDC();
	if(!$conn || !$toolname) {
		return(array());
	}

	$toolinfo = array();
	$tools = acc_gettoolnametools($toolname);
	if($tools) {
		for($i = 0; $i < count($tools); $i++) {
			if(!$published || isset($tools[$i]['state']) && strtolower($tools[$i]['state']) == 'published') {
				$toolinfo = $tools[$i];
				$i = count($tools) + 1;
			}
		}
	}
	return($toolinfo);
}


function acc_gettoolnametools($toolname, $alltools = null) {
	global $hub;

	$conn =& XFactory::getLDC();
	if(!$conn || !$toolname) {
		return(array());
	}

	$login = null;
	if($alltools !== true) {
		$juser =& JFactory::getUser();
		$login = $juser->get('username');
	}

	$toolnametoolsinfo = array();
	$toolnametools = acc_gettoolname($toolname);
	if(isset($toolnametools['member'])) {
		for($i = 0; $i < count($toolnametools['member']); $i++) {
			$tool = $toolnametools['member'][$i];
			if($tool) {
				$toolinfo = acc_gettool($tool);
				if($toolinfo) {
					if($toolinfo['public'] || $alltools === true) {
						array_push($toolnametoolsinfo, $toolinfo);
					}
					elseif($login) {
						if(!acc_gettoolaccess($tool, $login)) {
							array_push($toolnametoolsinfo, $toolinfo);
						}
					}
				}
			}
		}
		arraykeyedsort($toolnametoolsinfo, array('revision'), true);
	}
	return($toolnametoolsinfo);
}


function acc_gettoolname($toolname) {
	global $hub;

	$conn =& XFactory::getLDC();
	if(!$conn || !$toolname) {
		return(array());
	}

	$ldap_base_dn = "ou=toolnames," . $hub['ldap_base'];

	$toolnameinfo = array();
	$reqattr = array();
	$reqattr[] = 'toolName';
	$reqattr[] = 'cn';
	$reqattr[] = 'member';
	$toolnameentry = ldap_search($conn, $ldap_base_dn, "(toolname=" . $toolname . ")", $reqattr, 0, 0, 0, 3);
	$count = ldap_count_entries($conn, $toolnameentry);
	if($count > 0) {
		$firstentry = ldap_first_entry($conn, $toolnameentry);
		$attr = ldap_get_attributes($conn, $firstentry);
		if(isset($attr[$reqattr[0]][0])) {
			$toolnameinfo['toolname'] = $attr[$reqattr[0]][0];
		}
		if(isset($attr[$reqattr[1]][0])) {
			$toolnameinfo['name'] = $attr[$reqattr[1]][0];
		}
		if(isset($attr[$reqattr[2]][0])) {
			$toolnameinfo['member'] = array();
			for($i = 0; $i < count($attr[$reqattr[2]]); $i++) {
				if(isset($attr[$reqattr[2]][$i])) {
					$id = acc_extractfromdn($attr[$reqattr[2]][$i]);
					array_push($toolnameinfo['member'], $id);
				}
			}
		}
	}
	return($toolnameinfo);
}


function acc_gettooltoolname($tool) {
	global $hub;

	$conn =& XFactory::getLDC();
	if(!$conn || !$tool) {
		return(array());
	}

	$ldap_tool_base_dn = "ou=tools," . $hub['ldap_base'];
	$ldap_toolname_base_dn = "ou=toolnames," . $hub['ldap_base'];

	$toolnameinfo = array();
	$reqattr = array();
	$reqattr[] = 'toolName';
	$reqattr[] = 'cn';
	$reqattr[] = 'member';
	$toolnameentry = ldap_search($conn, $ldap_toolname_base_dn, "(member=tool=" . $tool . "," . $ldap_tool_base_dn . ")", $reqattr, 0, 0, 0, 3);
	$count = ldap_count_entries($conn, $toolnameentry);
	if($count > 0) {
		$firstentry = ldap_first_entry($conn, $toolnameentry);
		$attr = ldap_get_attributes($conn, $firstentry);
		if(isset($attr[$reqattr[0]][0])) {
			$toolnameinfo['toolname'] = $attr[$reqattr[0]][0];
		}
		if(isset($attr[$reqattr[1]][0])) {
			$toolnameinfo['name'] = $attr[$reqattr[1]][0];
		}
		if(isset($attr[$reqattr[2]][0])) {
			$toolnameinfo['member'] = array();
			for($i = 0; $i < count($attr[$reqattr[2]]); $i++) {
				if(isset($attr[$reqattr[2]][$i])) {
					$id = acc_extractfromdn($attr[$reqattr[2]][$i]);
					array_push($toolnameinfo['member'], $id);
				}
			}
		}
	}
	return($toolnameinfo);
}


function acc_gettoolgroupname($toolgroup) {
	global $hub;

	$conn =& XFactory::getLDC();
	if(!$conn) {
		return('');
	}

	$ldap_base_dn = "ou=toolgroups," . $hub['ldap_base'];

	$name = '';
	$reqattr = array('cn');
	$toolgroupentry = ldap_search($conn, $ldap_base_dn, "(toolgroup=" . $toolgroup . ")", $reqattr, 0, 0, 0, 3);
	$count = ldap_count_entries($conn, $toolgroupentry);
	if($count > 0) {
		$firstentry = ldap_first_entry($conn, $toolgroupentry);
		$attr = ldap_get_attributes($conn, $firstentry);
		$name = $attr[$reqattr[0]][0];
	}
	return($name);
}


function acc_licensetool($adm, $licenseid, $toolid) {
    global $hub;

    $conn =& XFactory::getPLDC();
    if(!$conn) {
        return;
	}

	$ldap_tool_str = "tool=[toolid],ou=tools," . $hub['ldap_base'];
	$ldap_license_str = "license=[licenseid],ou=licenses," . $hub['ldap_base'];

	if($licenseid && $toolid) {
		$ldap_tool_dn = str_replace("[toolid]", $toolid, $ldap_tool_str);
		$ldap_license_dn = str_replace("[licenseid]", $licenseid, $ldap_license_str);
		$attr = array("member" => $ldap_tool_dn);
		$logstr = "tool=" . quote($toolid);
		acc_log('license_tool', $adm, $licenseid, $logstr);
		return(@ldap_mod_add($conn, $ldap_license_dn, $attr));
	}
	return(0);
}


function acc_delicensetool($adm, $licenseid, $toolid) {
    global $hub;

    $conn =& XFactory::getPLDC();
    if(!$conn) {
        return;
	}

	$ldap_tool_str = "tool=[toolid],ou=tools," . $hub['ldap_base'];
	$ldap_license_str = "license=[licenseid],ou=licenses," . $hub['ldap_base'];

	if($licenseid && $toolid) {
		$ldap_tool_dn = str_replace("[toolid]", $toolid, $ldap_tool_str);
		$ldap_license_dn = str_replace("[licenseid]", $licenseid, $ldap_license_str);
		$attr = array("member" => $ldap_tool_dn);
		$logstr = "tool=" . quote($toolid);
		acc_log('delicense_tool', $adm, $licenseid, $logstr);
		return(ldap_mod_del($conn, $ldap_license_dn, $attr));
	}
	return(0);
}

?>
