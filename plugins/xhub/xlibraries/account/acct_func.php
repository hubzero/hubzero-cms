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

function acc_dbopen(&$db) {
	global $usagestats_dbhost, $usagestats_username, $usagestats_password, $usagestats_database;

	$db = mysql_connect($usagestats_dbhost, $usagestats_username, $usagestats_password);
	mysql_select_db($usagestats_database, $db);
}

function acc_dbclose() {
	mysql_close();
}

function acc_invalidlogin($message = '') {
	echo "<h1>Invalid Login</h1>\n";
	if($message) {
		echo "<p>" . $message . "</p>\n";
	}
}

function acc_error($action = '', $admin_email = '') {
    global $hub;

	if(!$admin_email) {
		$admin_email = acc_getadminemail();
	}
	$html = '<p>There was an error';
	if($action) {
		$html .= ' '. $action;
	}
	$html .= '.</p>'."\n";
	$html .= '<p>Please try again or <a href="mailto:'. htmlentities($admin_email) .'">contact hub administrators</a> for assistance.</p>'."\n";
	echo $html;
}

function acc_email($email, $subject, $message) {
    global $hub;

	if($hub) {
		$contact_email = $hub['email'];
		$contact_name = $hub['name'] . " Administrator";

		$args = "-f '" . $contact_email . "'";
		$headers = "MIME-Version: 1.0\n";
		$headers .= "Content-type: text/plain; charset=iso-8859-1\n";
		$headers .= "From: " . $contact_name . " <" . $contact_email . ">\n";
		$headers .= "Reply-To: " . $contact_name . " <" . $contact_email . ">\n";
		$headers .= "X-Priority: 3\n";
		$headers .= "X-MSMail-Priority: High\n";
		$headers .= "X-Mailer: " . $hub['name'] . "\n";
		if(mail($email, $subject, $message, $headers, $args)) {
			return(1);
		}
	}
	return(0);
}

function acc_userpassgen($length = 8) {
    $genpass = '';
	$salt = "abchefghjkmnpqrstuvwxyz0123456789";
	srand((double)microtime()*1000000);
	$i = 0;
	while ($i < $length) {
		$num = rand() % 33;
		$tmp = substr($salt, $num, 1);
		$genpass = $genpass . $tmp;
		$i++;
	}
	return($genpass);
}

function acc_userpasscheck($password1, $password2) {
	if($password1 == $password2)
		return true;
	else
		return false;
}

function acc_validlogin($login) {
	if(eregi("^[0-9a-zA-Z]+[_0-9a-zA-Z]*$", $login)) {
		if(is_positiveint($login)) {
			return(0);
		}
		else {
			return(1);
		}
	}
	else {
		return(0);
	}
}

function acc_validgid($gid) {
	if(eregi("^[0-9a-zA-Z]+[_0-9a-zA-Z]*$", $gid)) {
		if(is_positiveint($gid)) {
			return(0);
		}
		else {
			return(1);
		}
	}
	else {
		return(0);
	}
}

function acc_validpassword($password) {
	if(eregi("^[_\`\~\!\@\#\$\%\^\&\*\(\)\=\+\{\}\:\;\"\'\<\>\,\.\?\/0-9a-zA-Z-]+$", $password)) {
		return(1);
	}
	else {
		return(0);
	}
}

function acc_validemail($email) {
	if(eregi("^[_\.\%0-9a-zA-Z-]+@([0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$", $email)) {
		return(1);
	}
	else {
		return(0);
	}
}

function acc_validurl($url) {
	if(eregi("^[_\`\~\!\@\#\$\%\^\&\*\(\)\=\+\{\}\:\;\"\'\<\>\,\.\?\/0-9a-zA-Z-]*$", $url)) {
		return(1);
	}
	else {
		return(0);
	}
}

function acc_validphone($phone) {
	if(eregi("^[\ \#\*\+\:\,\.0-9-]*$", $phone)) {
		return(1);
	}
	else {
		return(0);
	}
}

function acc_validtext($text) {
	if(!strchr($text, "	")) {
		return(1);
	}
	else {
		return(0);
	}
}

function acc_validhome($text) {
	if(!strchr($text, "	")) {
		return(1);
	}
	else {
		return(0);
	}
}

function acc_validjobs($jobs) {
	if(eregi("^[0-9]+$", $jobs)) {
		return(1);
	}
	else {
		return(0);
	}
}

function acc_validgroupname($name) {
	if(eregi("^[ \,\.\/0-9a-zA-Z-]+$", $name)) {
		return(1);
	}
	else {
		return(0);
	}
}

function acc_getadminemail() {
    global $hub;

	return($hub['email']);
}

function acc_getadminsysemail() {
    global $hub;

	return($hub['sysemail']);
}

function acc_genemailconfirm() {
    return(-rand(1, pow(2, 31)-1)); // php5 in debian etch returns negative values if i don't subtract 1 from this max 
}

function acc_genhomedir($login) {
    global $hub;

	return($hub['home'] . "/" . $login);
}

function acc_encryptpassword($password) {
	return("{MD5}" . base64_encode(pack('H*', md5($password))));
}

function acc_getorgs() {
    global $hub;

    $conn =& XFactory::getLDC();

    if (!$conn)
        return;

	$ldap_base_dn = "ou=orgs," . $hub['ldap_base'];
	$ldap_search_str = "(o=*)";

	$getorgs = array();
	if($conn) {
		$reqattr = array();
		array_push($reqattr, "o");
		$course = array();
		$courseentry = ldap_search($conn, $ldap_base_dn, $ldap_search_str, $reqattr, 0, 0, 0, 3);
		$entry = ldap_first_entry($conn, $courseentry);
		while($entry) {
			$attr = ldap_get_attributes($conn, $entry);
			array_push($getorgs, $attr[$reqattr[0]][0]);
			$entry = ldap_next_entry($conn, $entry);
		}
		sort($getorgs);
	}
	return($getorgs);
}

// Log any account management action ($action) on hub ($hub)
// by user ($login) on object ($target) with details ($logstr)
// $logstr is to take the form:
//    item1="quoted value" item2="quoted value" ...
//    Use the quote() function for quoting values correctly!
function acc_log($action, $login, $target, $logstr) {
    global $hub;

	$dt = date('Y-m-d H:i:s');
	if(!$hub['name']) {
		$hubname = "-";
	}
	else {
		$hubname = $hub['name'];
	}
	if(!$action) {
		$action = "-";
	}
	if(!$login) {
		$login = "-";
	}
	if(!$target) {
		$target = "-";
	}
	$line = $dt . " " . $hubname . " " . $action . " " . $login . " " . $target . " " . $logstr . "\n";
	$fp = null;
	while(!$fp) {
		$fp = fopen("/var/log/hub/account.log", "a+");
	}
	fputs($fp, $line);
	fclose($fp);
}

?>
