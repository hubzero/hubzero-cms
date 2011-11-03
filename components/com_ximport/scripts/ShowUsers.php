<?php
/**
 * @package     hubzero-cms
 * @author      Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright   Copyright 2008-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 *
 * Copyright 2008-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

ximport('Hubzero_User_Profile');

class ShowUsers extends XImportHelperScript
{
	protected $_description = 'Show users from LDAP.';

	public function run() 
	{
		Hubzero_User_Profile_Helper::iterate_profiles('printuser','ldap');
		return;
	}
}

$mycount = 0;

function printuser($name)
{
	global $mycount;

	if ($mycount > 1000) {
		exit();
	}

	$profile = new Hubzero_User_Profile();
	$profile2 = new Hubzero_User_Profile();

	$profile->load($name,'ldap');
	$profile2->load($name,'mysql');

	if (($profile2->get('uidNumber') == '0') || ($profile2->get('uidNumber') == ''))
	{
		$mycount++;
		$profile->create('mysql');
		echo "no xprofile entry for $name<br />";
		echo "created one<br />";
	}
	else if ($profile->get('uidNumber') != $profile2->get('uidNumber')) 
	{
		echo "profile mismatch name $name " . $profile->get('uidNumber') . " " . $profile2->get('uidNumber') . '<br />';
	}

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
