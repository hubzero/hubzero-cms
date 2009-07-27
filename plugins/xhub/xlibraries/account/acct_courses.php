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

function acc_gencourseid($school, $number) {
	$courseid = preg_replace("/\s/", "", $number);
	$courseid = strtolower($courseid);
	if(acc_courseidexists($courseid)) {
		$s = preg_replace("/\s/", "", $school);
		$courseid = $courseid . strtolower($s);
		if(acc_courseidexists($courseid)) {
			$ext = 2;
			$s = $courseid . "-" . trim($ext);
			while(acc_courseidexists($s)) {
				$ext++;
				$s = $courseid . "-" . trim($ext);
			}
			$courseid = $s;
		}
	}
	return($courseid);
}

function acc_courseidexists($courseid) {
    global $hub;

    $conn =& XFactory::getLDC();

    if (!$conn || !$courseid)
        return(0);

	$ldap_base_dn = "ou=courses," . $hub['ldap_base'];
	$ldap_search_str = "(courseid=[courseid])";

	$ldap_search_str = str_replace("[courseid]", $courseid, $ldap_search_str);
	$reqattr = array();
	$entry = ldap_search($conn, $ldap_base_dn, $ldap_search_str, $reqattr, 0, 0, 0, 3);
	$count = ldap_count_entries($conn, $entry);
	return($count > 0);
}

function acc_courseexists($school, $number) {
    global $hub;

    $conn =& XFactory::getLDC();

    if (!$conn || !$school || !$number)
        return(0);

	$ldap_base_dn = "ou=courses," . $hub['ldap_base'];
	$ldap_search_str = "(&(o=[school])(courseNumber=[number]))";

	$ldap_search_str = str_replace("[school]", $school, $ldap_search_str);
	$ldap_search_str = str_replace("[number]", $number, $ldap_search_str);
	$reqattr = array();
	$entry = ldap_search($conn, $ldap_base_dn, $ldap_search_str, $reqattr, 0, 0, 0, 3);
	$count = ldap_count_entries($conn, $entry);
	return($count > 0);
}

function acc_getcourseinstructorsemail($courseid) {
    global $hub;

    $conn =& XFactory::getLDC();

    if (!$conn)
        return "";

	$ldap_course_str = "courseid=[courseid],ou=courses," . $hub['ldap_base'];
	$ldap_search_str = "(owner=*)";

	$instructorsemail = "";

	$ldap_course_dn = str_replace("[courseid]", $courseid, $ldap_course_str);
	$reqattr = array("owner");
	$courseentry = ldap_search($conn, $ldap_course_dn, $ldap_search_str, $reqattr, 0, 0, 0, 3);
	$entry = ldap_first_entry($conn, $courseentry);
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
			$reqattr = array("mail");
			$userentry = ldap_search($conn, $ldap_owner_dn, "(objectclass=*)", $reqattr, 0, 0, 0, 3);
			$entry = ldap_first_entry($conn, $userentry);
			$ldap_owner_dns = array();
			if($entry) {
				$attr = ldap_get_attributes($conn, $entry);
				if($instructorsemail) {
					$instructorsemail .= ",";
				}
				$instructorsemail .= $attr[$reqattr[0]][0];
			}
		}
	}

	return($instructorsemail);
}

function acc_coursecreate($adm, $school, $number, $name, $comment) {
    global $hub;

    $conn =& XFactory::getPLDC();

    if (!$conn || !$school || !$number || !$name)
        return null;

	$ldap_add_str = "courseid=[courseid],ou=courses," . $hub['ldap_base'];
	$ldap_user_str = "uid=[login],ou=users," . $hub['ldap_base'];

	$courseid = acc_gencourseid($school, $number);
	$ldap_add_dn = str_replace("[courseid]", $courseid, $ldap_add_str);
	$ldap_user_dn = str_replace("[login]", $adm, $ldap_user_str);
	$attr = array();
	$attr["objectclass"] = array();
	$attr["objectclass"][0] = "top";
	$attr["objectclass"][1] = "hubCourse";
	$attr["courseid"] = $courseid;
	$attr["o"] = $school;
	$attr["courseNumber"] = $number;
	$attr["cn"] = $name;
	if($comment) {
		$attr["description"] = $comment;
	}
	$attr["owner"] = $ldap_user_dn;
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
		acc_log('course_create', $adm, $courseid, $logstr);
		return($courseid);
	}
	return(null);
}

function acc_courseconfirm($adm, $courseid) {
    global $hub;

    $conn =& XFactory::getPLDC();

    if (!$conn || !$courseid)
        return;

	$ldap_mod_str = "courseid=[courseid],ou=courses," . $hub['ldap_base'];

	$ldap_mod_dn = str_replace("[courseid]", $courseid, $ldap_mod_str);
	$attr = array("public" => "TRUE");
	
	acc_log('course_confirm', $adm, $courseid, "");

    if(ldap_mod_replace($conn, $ldap_mod_dn, $attr))
		return(1);

	return(ldap_mod_add($conn, $ldap_mod_dn, $attr));
}

function acc_courseunconfirm($adm, $courseid) {
    global $hub;

    $conn =& XFactory::getPLDC();

    if (!$conn || !$courseid)
        return;

	$ldap_mod_str = "courseid=[courseid],ou=courses," . $hub['ldap_base'];

	$ldap_mod_dn = str_replace("[courseid]", $courseid, $ldap_mod_str);
	$attr = array("public" => "FALSE");
	
	acc_log('course_unconfirm', $adm, $courseid, "");

	if(ldap_mod_replace($conn, $ldap_mod_dn, $attr)) 
		return(1);

	return(ldap_mod_add($conn, $ldap_mod_dn, $attr));
}

function acc_courseupdate($adm, $courseid, $school, $number, $name, $comment) {
    global $hub;

    $conn =& XFactory::getPLDC();

    if (!$conn || !$courseid)
        return;

	$ldap_mod_str = "courseid=[courseid],ou=courses," . $hub['ldap_base'];

	$ldap_mod_dn = str_replace("[courseid]", $courseid, $ldap_mod_str);
	$attr = array();
	if($school) {
		$attr["o"] = $school;
	}
	if($number) {
		$attr["courseNumber"] = $number;
	}
	if($name) {
		$attr["cn"] = $name;
	}
	if($comment) {
		$attr["description"] = $comment;
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

	acc_log('course_update', $adm, $courseid, $logstr);
	
	return(ldap_mod_replace($conn, $ldap_mod_dn, $attr));
}

function acc_usercoursereg($adm, $login, $courseid) {
    global $hub;

    $conn =& XFactory::getPLDC();

    if (!$conn || !$courseid || !$login)
        return;

	$ldap_mod_str = "courseid=[courseid],ou=courses," . $hub['ldap_base'];
	$ldap_user_str = "uid=[login],ou=users," . $hub['ldap_base'];

	$ldap_mod_dn = str_replace("[courseid]", $courseid, $ldap_mod_str);
	$ldap_user_dn = str_replace("[login]", $login, $ldap_user_str);
	$attr = array("applicant" => $ldap_user_dn);
	$logstr = "courseid=" . quote($courseid);
	
	acc_log('user_course_apply', $adm, $login, $logstr);
	
	return(ldap_mod_add($conn, $ldap_mod_dn, $attr));
}

function acc_usercourseunreg($adm, $login, $courseid) {
    global $hub;

    $conn =& XFactory::getPLDC();

    if (!$conn || !$courseid || !$login)
        return 0;

	$ldap_mod_str = "courseid=[courseid],ou=courses," . $hub['ldap_base'];
	$ldap_user_str = "uid=[login],ou=users," . $hub['ldap_base'];

	$ret = 0;

	$ldap_mod_dn = str_replace("[courseid]", $courseid, $ldap_mod_str);
	$ldap_user_dn = str_replace("[login]", $login, $ldap_user_str);

	$filter = "applicant=" . $ldap_user_dn;
	$entry = ldap_search($conn, $ldap_mod_dn, $filter, array(), 0, 0, 0, 3);
	$count = ldap_count_entries($conn, $entry);
	if($count) {
		$attr = array("applicant" => $ldap_user_dn);
		if(ldap_mod_del($conn, $ldap_mod_dn, $attr)) {
			$ret = 1;
		}
	}

	$filter = "member=" . $ldap_user_dn;
	$entry = ldap_search($conn, $ldap_mod_dn, $filter, array(), 0, 0, 0, 3);
	$count = ldap_count_entries($conn, $entry);
	if($count) {
		$attr = array("member" => $ldap_user_dn);
		if(ldap_mod_del($conn, $ldap_mod_dn, $attr)) {
			$ret = 1;
		}
	}

	$filter = "owner=" . $ldap_user_dn;
	$entry = ldap_search($conn, $ldap_mod_dn, $filter, array(), 0, 0, 0, 3);
	$count = ldap_count_entries($conn, $entry);
	if($count) {
		$attr = array("owner" => $ldap_user_dn);
		if(ldap_mod_del($conn, $ldap_mod_dn, $attr)) {
			$ret = 1;
		}
	}
	if($ret) {
		$logstr = "courseid=" . quote($courseid);
		acc_log('user_course_remove', $adm, $login, $logstr);
	}

	return($ret);
}

function acc_usercourseconfirm($adm, $login, $courseid) {
    global $hub;

    $conn =& XFactory::getPLDC();

    if (!$conn || !$courseid || !$login)
        return 0;

	$ldap_mod_str = "courseid=[courseid],ou=courses," . $hub['ldap_base'];
	$ldap_user_str = "uid=[login],ou=users," . $hub['ldap_base'];

	$ldap_mod_dn = str_replace("[courseid]", $courseid, $ldap_mod_str);
	$ldap_user_dn = str_replace("[login]", $login, $ldap_user_str);
	$attr = array("member" => $ldap_user_dn);

	if(ldap_mod_add($conn, $ldap_mod_dn, $attr)) {
		$logstr = "courseid=" . quote($courseid);

		acc_log('user_course_approve', $adm, $login, $logstr);

		$attr = array("applicant" => $ldap_user_dn);

		return(ldap_mod_del($conn, $ldap_mod_dn, $attr));
	}
	return(0);
}

function acc_usercourseinstruct($adm, $login, $courseid) {
    global $hub;

    $conn =& XFactory::getPLDC();

    if (!$conn || !$courseid || !$login)
        return 0;

	$ldap_mod_str = "courseid=[courseid],ou=courses," . $hub['ldap_base'];
	$ldap_user_str = "uid=[login],ou=users," . $hub['ldap_base'];

	$ldap_mod_dn = str_replace("[courseid]", $courseid, $ldap_mod_str);
	$ldap_user_dn = str_replace("[login]", $login, $ldap_user_str);
	$attr = array("owner" => $ldap_user_dn);
	
	if(ldap_mod_add($conn, $ldap_mod_dn, $attr)) {
		$logstr = "courseid=" . quote($courseid);
		
		acc_log('user_course_setmanager', $adm, $login, $logstr);
		$attr = array("member" => $ldap_user_dn);
		
		return(ldap_mod_del($conn, $ldap_mod_dn, $attr));
	}

	return(0);
}

function acc_usercourseuninstruct($adm, $login, $courseid) {
    global $hub;

    $conn =& XFactory::getPLDC();

    if (!$conn || !$courseid || !$login)
        return 0;

	$ldap_mod_str = "courseid=[courseid],ou=courses," . $hub['ldap_base'];
	$ldap_user_str = "uid=[login],ou=users," . $hub['ldap_base'];

	$ldap_mod_dn = str_replace("[courseid]", $courseid, $ldap_mod_str);
	$ldap_user_dn = str_replace("[login]", $login, $ldap_user_str);
	$attr = array("member" => $ldap_user_dn);
	
	if(ldap_mod_add($conn, $ldap_mod_dn, $attr)) {
		$logstr = "courseid=" . quote($courseid);
		
		acc_log('user_course_unsetmanager', $adm, $login, $logstr);
		
		$attr = array("owner" => $ldap_user_dn);
		
		return(ldap_mod_del($conn, $ldap_mod_dn, $attr));
	}

	return(0);
}

function acc_getusercourses($login) {
    global $hub;

    $conn =& XFactory::getLDC();

    if (!$conn)
        return 0;

	$ldap_base_dn = $hub['ldap_base'];
	$ldap_owner_search_str = "(owner=uid=[login],ou=users," . $hub['ldap_base'] . ")";
	$ldap_member_search_str = "(member=uid=[login],ou=users," . $hub['ldap_base'] . ")";
	$ldap_applicant_search_str = "(applicant=uid=[login],ou=users," . $hub['ldap_base'] . ")";

	$getusercourses = array();

	$reqattr = array();
	array_push($reqattr, 'courseid');
	$filter = str_replace("[login]", $login, $ldap_owner_search_str);
	$courseentry = ldap_search($conn, $ldap_base_dn, $filter, $reqattr, 0, 0, 0, 3);
	$entry = ldap_first_entry($conn, $courseentry);
	while($entry) {
		$attr = ldap_get_attributes($conn, $entry);
		$course['courseid']     = (isset($attr[$reqattr[0]][0])) ? $attr[$reqattr[0]][0]: NULL;
		$course['instructor']   = 1;
		$course['regconfirmed'] = 1;
		array_push($getusercourses, $course);
		$entry = ldap_next_entry($conn, $entry);
	}

	$reqattr = array();
	array_push($reqattr, "courseid");
	$filter = str_replace("[login]", $login, $ldap_member_search_str);
	$courseentry = ldap_search($conn, $ldap_base_dn, $filter, $reqattr, 0, 0, 0, 3);
	$entry = ldap_first_entry($conn, $courseentry);
	while($entry) {
		$attr = ldap_get_attributes($conn, $entry);
		$course['courseid']     = (isset($attr[$reqattr[0]][0])) ? $attr[$reqattr[0]][0]: NULL;
		$course['instructor']   = 0;
		$course['regconfirmed'] = 1;
		array_push($getusercourses, $course);
		$entry = ldap_next_entry($conn, $entry);
	}

	$reqattr = array();
	array_push($reqattr, "courseid");
	$filter = str_replace("[login]", $login, $ldap_applicant_search_str);
	$courseentry = ldap_search($conn, $ldap_base_dn, $filter, $reqattr, 0, 0, 0, 3);
	$entry = ldap_first_entry($conn, $courseentry);
	while($entry) {
		$attr = ldap_get_attributes($conn, $entry);
		$course['courseid'] = $attr[$reqattr[0]][0];
		$course['instructor'] = 0;
		$course['regconfirmed'] = 0;
		array_push($getusercourses, $course);
		$entry = ldap_next_entry($conn, $entry);
	}

	return($getusercourses);
}

function acc_isusercourseinstructor($login, $courseid = null) {
    global $hub;

    $conn =& XFactory::getLDC();

    if (!$conn || !$courseid || !$login)
        return 0;

	if (!empty($courseid))
	    $ldap_base_str = "courseid=[courseid],ou=courses," . $hub['ldap_base'];
	else
	    $ldap_base_str = "ou=courses," . $hub['ldap_base'];

	$ldap_search_str = "(owner=uid=[login],ou=users," . $hub['ldap_base'] . ")";

	$ldap_base_dn = str_replace("[courseid]", $courseid, $ldap_base_str);
	$ldap_search_str = str_replace("[login]", $login, $ldap_search_str);
	$reqattr = array();
	$userentry = ldap_search($conn, $ldap_base_dn, $ldap_search_str, $reqattr, 0, 0, 0, 3);
	$count = ldap_count_entries($conn, $userentry);
	
	return ($count > 0);
}

function acc_getcourseusers($courseid, $usertype) {
    global $hub;

    $conn =& XFactory::getLDC();

    if (!$conn || !$courseid)
        return null;

	$ldap_base_dn = "ou=courses," . $hub['ldap_base'];
	$ldap_search_str = "(courseid=[courseid])";

	$ldap_search_str = str_replace("[courseid]", $courseid, $ldap_search_str);
	if($usertype == 0) {
		$coursereqattr = array("applicant");
	}
	elseif($usertype == 1) {
		$coursereqattr = array("member");
	}
	elseif($usertype == 2) {
		$coursereqattr = array("owner");
	}
	$courseentry = ldap_search($conn, $ldap_base_dn, $ldap_search_str, $coursereqattr, 0, 0, 0, 3);
	$count = ldap_count_entries($conn, $courseentry);
	if($count > 0) {
		$entry = ldap_first_entry($conn, $courseentry);
		$courseattr = ldap_get_attributes($conn, $entry);
		$courseusers = array();
		$userreqattr = array();
		array_push($userreqattr, "uid");
		array_push($userreqattr, "emailConfirmed");
		array_push($userreqattr, "homeDirectory");
		array_push($userreqattr, "jobsAllowed");
		array_push($userreqattr, "regDate");
		array_push($userreqattr, "regIp");
		array_push($userreqattr, "regHost");
		array_push($userreqattr, "cn");
		array_push($userreqattr, "mail");
		array_push($userreqattr, "o");
		array_push($userreqattr, "url");
		array_push($userreqattr, "homePhone");
		array_push($userreqattr, "description");
		for($i = 0; $i < count($courseattr[$coursereqattr[0]]); $i++) {
			$ldap_user_dn = $courseattr[$coursereqattr[0]][$i];
			if($ldap_user_dn) {
				$userentry = ldap_search($conn, $ldap_user_dn, "(objectclass=*)", $userreqattr, 0, 0, 0, 3);
				$count = ldap_count_entries($conn, $userentry);
				if($count > 0) {
					$firstentry = ldap_first_entry($conn, $userentry);
					$attr = ldap_get_attributes($conn,  $firstentry);
					$user = array();
					$user['login'] = $attr[$userreqattr[0]][0];
					$user['email_confirmed'] = $attr[$userreqattr[1]][0];
					$user['home'] = $attr[$userreqattr[2]][0];
					$user['jobs_allowed'] = $attr[$userreqattr[3]][0];
					$user['reg_date'] = $attr[$userreqattr[4]][0];
					$user['reg_ip'] = $attr[$userreqattr[5]][0];
					$user['reg_host'] = $attr[$userreqattr[6]][0];
					$user['name'] = $attr[$userreqattr[7]][0];
					$user['email'] = $attr[$userreqattr[8]][0];
					$user['org'] = $attr[$userreqattr[9]][0];
					$user['web'] = $attr[$userreqattr[10]][0];
					$user['phone'] = $attr[$userreqattr[11]][0];
					$user['reason'] = $attr[$userreqattr[12]][0];
					array_push($courseusers, $user);
				}
			}
		}
		return($courseusers);
	}
}

function acc_getcourse($courseid) {
    global $hub;

    $conn =& XFactory::getLDC();

    if (!$conn)
        return null;

	$ldap_base_dn = "ou=courses,".$hub['ldap_base'];
	$ldap_search_str = "(courseid=[courseid])";

	$ldap_search_str = str_replace("[courseid]", $courseid, $ldap_search_str);
	$reqattr = array();
	array_push($reqattr, "courseid");
	array_push($reqattr, "courseNumber");
	array_push($reqattr, "o");
	array_push($reqattr, "cn");
	array_push($reqattr, "description");
	array_push($reqattr, "public");
	$course = array();
	$courseentry = ldap_search($conn, $ldap_base_dn, $ldap_search_str, $reqattr, 0, 0, 0, 3);
	$entry = ldap_first_entry($conn, $courseentry);
	if($entry) {
		$attr = ldap_get_attributes($conn, $entry);
		$course['courseid'] = $attr[$reqattr[0]][0];
		$course['number'] = $attr[$reqattr[1]][0];
		$course['school'] = $attr[$reqattr[2]][0];
		$course['name'] = $attr[$reqattr[3]][0];
		$course['comment'] = $attr[$reqattr[4]][0];
		$course['confirmed'] = $attr[$reqattr[5]][0];
		return($course);
	}
	return(null);
}

function acc_getcourses($confirmed = 1) {
    global $hub;

    $conn =& XFactory::getLDC();

    if (!$conn)
        return array();

	$ldap_base_dn = $hub['ldap_base'];
	$ldap_search_str = "(courseid=*)";

	$getcourses = array();

	$reqattr = array();
	array_push($reqattr, 'courseid');
	array_push($reqattr, 'courseNumber');
	array_push($reqattr, 'o');
	array_push($reqattr, 'cn');
	array_push($reqattr, 'description');
	array_push($reqattr, 'public');
	$course = array();
	$courseentry = ldap_search($conn, $ldap_base_dn, $ldap_search_str, $reqattr, 0, 0, 0, 3);
	$entry = ldap_first_entry($conn, $courseentry);
	while($entry) {
		$attr = ldap_get_attributes($conn, $entry);
		$course['courseid']  = (isset($attr[$reqattr[0]][0])) ? $attr[$reqattr[0]][0]: NULL;
		$course['number']    = (isset($attr[$reqattr[1]][0])) ? $attr[$reqattr[1]][0]: NULL;
		$course['school']    = (isset($attr[$reqattr[2]][0])) ? $attr[$reqattr[2]][0]: NULL;
		$course['name']      = (isset($attr[$reqattr[3]][0])) ? $attr[$reqattr[3]][0]: NULL;
		$course['comment']   = (isset($attr[$reqattr[4]][0])) ? $attr[$reqattr[4]][0]: NULL;
		$course['confirmed'] = (isset($attr[$reqattr[5]][0])) ? $attr[$reqattr[5]][0]: NULL;
		if($confirmed && $course['confirmed'] || !$confirmed) {
			array_push($getcourses, $course);
		}
		$entry = ldap_next_entry($conn, $entry);
	}
	arraykeyedsort($getcourses, array("school", "number"));

	return($getcourses);
}

?>
