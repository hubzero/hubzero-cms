<?php
/**
 * @package		HUBzero CMS
 * @author		Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright	Copyright 2008-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2008-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
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

ximport('xprofile');
ximport('Hubzero_Users_Password');

$mycount = 0;

function _comparepasswords($mode = 0)
{
	$mycount = 0;
	$xhub = &XFactory::getHub();
	$conn = &XFactory::getPLDC();
	$db = &JFactory::getDBO();

    $hubLDAPBaseDN = $xhub->getCfg('hubLDAPBaseDN');

    $dn = 'ou=users,' . $hubLDAPBaseDN;
    //$filter = '(&(objectclass=*)(hasSubordinates=FALSE)(uidNumber=1546))';
    //$filter = '(&(objectclass=*)(hasSubordinates=FALSE)(uidNumber=15825))';
    //$filter = '(&(objectclass=*)(hasSubordinates=FALSE)(uidNumber=10617))';
    $filter = '(&(objectclass=*)(hasSubordinates=FALSE))';

    $sr = @ldap_search($conn, $dn, $filter, array("*","+")); //, $attributes, 0, 0, 0);

    if ($sr === false)
    	return false;

    $count = @ldap_count_entries($conn, $sr);

    if ($count === false)
    	return false;

    $entry = @ldap_first_entry($conn, $sr);

	echo "<table>";
	echo "<tr><td>username</td><td>ldap</td><td>mysql</td><td>pass</td><td>action</td></tr>";
    
	do
    {	
   		$attributes = ldap_get_attributes($conn, $entry);
		$rowhtml = '';
		$showrow = false;

		$username = $attributes['uid'][0];

		$profile = new XProfile();
		$password = Hubzero_Users_Password::getInstance($username);
		$result = $profile->load($username);

		$ldaphash = (isset($attributes['userPassword']))?$attributes['userPassword'][0]:'';
		$profilehash = $profile->get('userPassword');
		$passhash = $password->get('passhash');

		if ($result === false) {
			echo 'couldn\'t find profile for ' . $attributes['uid'][0] . "<br>\n";
        	$entry = @ldap_next_entry($conn, $entry);
			continue;
		}

		if (0 && ($ldaphash == $profilehash) && ($ldaphash == $passhash))
		{
        	$entry = @ldap_next_entry($conn, $entry);
			continue;
		}

		echo "<tr>";
		echo "<td>" . $username . "</td>";
		echo "<td>" . $ldaphash . "</td>";
		if ($ldaphash != $profilehash) 
			echo "<td><b>" . $profilehash . "</b></td>";
		else
			echo "<td>" . $profilehash . "</td>";

		if ($ldaphash != $passhash)
			echo "<td><b>" . $passhash . "</b></td>";
		else
			echo "<td>" . $passhash . "</td>";


		if (($ldaphash == $passhash) && ($ldaphash != $profilehash))
		{
			$profile->set('userPassword',$ldaphash);
			$profile->update();
			echo "<td><b>Fixed profile hash</b></td>";
		}
		else 
		{
			if (($ldaphash != $passhash) && ($passhash == $profilehash) && ($passhash == '{MD5}QlUi4CvFlZ78b6YRKl7maQ=='))
			{
				$profile->set('userPassword',$ldaphash);
				$profile->update();
				$password->set('passhash',$ldaphash);
				$password->update();
				echo "<td><b>Fixed preset passwords</b></td>";
			}
			else
			{
				echo "<td>&nbsp;</td>";
			}
		}

		echo "</tr>";
		$mycount++;

        $entry = @ldap_next_entry($conn, $entry);
    }
    while($entry !== false);

	echo "count = $count<br>";
	echo "mycount = $mycount<br>";
}


function printpasswd($name)
{
	global $mycount;


	if ($mycount > 1000)
		exit();

	$profile = new XProfile();
	$profile2 = new XProfile();

	$profile->load($name,'ldap');
	$profile2->load($name,'mysql');

	if (($profile2->get('uidNumber') == '0') || ($profile2->get('uidNumber') == ''))
	{
	$mycount++;
		$profile->create('mysql');
		echo "no xprofile entry for $name<br>";
		echo "created one<br>";
	}
	else if ($profile->get('uidNumber') != $profile2->get('uidNumber'))
		echo "profile mismatch name $name " . $profile->get('uidNumber') . " " . $profile2->get('uidNumber') . "<br>";

	$mjuser = JUser::getInstance($name);

	if (empty($mjuser))
	{
	$mycount++;
		$instance = new JUser();
        $usertype = 'Registered';
        $acl =& JFactory::getACL();
        $instance->set( 'id', $profile->get('uidNumber') );
        $instance->set( 'name', $profile->get('name') );
        $instance->set( 'username', $profile->get('username'));
        $instance->set( 'password_clear', '' );
        $instance->set( 'email', $profile->get('email'));
        $instance->set( 'gid', $acl->get_group_id( '', $usertype));
        $instance->set( 'usertype', $usertype );
		$result = $instance->save();
		echo "no juser entry for $name<br>";
		echo "created one<br>";
		$db =& JFactory::getDBO();

		$sql = "INSERT INTO #__users (id,name,username,email,gid,usertype) VALUES (" .
			$db->Quote( $profile->get('uidNumber') ) . "," .
			$db->Quote( $profile->get('name') ) . "," .
			$db->Quote( $profile->get('username') ) . "," .
			$db->Quote( $profile->get('email') ) . "," .
			$db->Quote( $acl->get_group_id( '', $usertype) ) . "," .
			$db->Quote( $usertype ) .
			");";
	
		$db->setQuery($sql);
		$result = $db->query();

		if (!$result)
			die('db error');

		$sql = "INSERT INTO #__core_acl_aro (section_value,value,name) VALUES ( 'users' ," .
			$db->Quote(  $profile->get('uidNumber') ) . "," .
			$db->Quote( $profile->get('name') ) .
			");";
		$db->setQuery($sql);
		$result = $db->query();
		if (!$result)
			die('db error');
		$insertid = $db->insertid();

		$sql = "INSERT INTO #__core_acl_groups_aro_map (group_id, aro_id) VALUES ( " .
			 $db->Quote( $acl->get_group_id( '', $usertype) ) . "," .
			 $db->Quote( $insertid ) .
			");";
		$db->setQuery($sql);
		$result = $db->query();
		if (!$result)
			die('db error');
	}
	else if ($profile->get('uidNumber') != $mjuser->get('id'))
		echo "juser mismatch name $name " . $profile->get('uidNumber') . " " . $mjuser->get('id') . "<br>";

}

function _showusers() 
{
XProfileHelper::iterate_profiles('printuser','ldap');
return;
}
?>
