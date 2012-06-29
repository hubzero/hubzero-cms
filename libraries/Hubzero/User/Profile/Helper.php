<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 *
 * @package   hubzero-cms
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for 'Hubzero_User_Profile_Helper'
 * 
 * Long description (if any) ...
 */
class Hubzero_User_Profile_Helper
{

	/**
	 * Short description for 'iterate_profiles'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $func Parameter description (if any) ...
	 * @param      string $storage Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function iterate_profiles($func, $storage)
	{
		$db = &JFactory::getDBO();

		if (!empty($storage) && !in_array($storage,array('mysql','ldap')))
			return false;

		if ($storage == 'ldap')
		{
			$xhub = &Hubzero_Factory::getHub();
			$conn = &Hubzero_Factory::getPLDC();

			$ldap_params = JComponentHelper::getParams('com_ldap');
			$hubLDAPBaseDN = $ldap_params->get('ldap_basedn','');

			$dn = 'ou=users,' . $hubLDAPBaseDN;
			$filter = '(objectclass=posixAccount)';

			$attributes[] = 'uid';

			$sr = @ldap_search($conn, $dn, $filter, $attributes, 0, 0, 0);

			if ($sr === false)
				return false;

			$count = @ldap_count_entries($conn, $sr);

			if ($count === false)
				return false;

			$entry = @ldap_first_entry($conn, $sr);

			do
			{
				$attributes = ldap_get_attributes($conn, $entry);
				$func($attributes['uid'][0]);
				$entry = @ldap_next_entry($conn, $entry);
			}
			while($entry !== false);
		}

		if ($storage == 'mysql')
		{
			$query = "SELECT uidNumber FROM #__xprofiles;";

			$db->setQuery($query);

			$result = $db->query();

			if ($result === false)
			{
				$this->setError('Error retrieving data from xprofiles table: ' . $db->getErrorMsg());
				return false;
			}

			while ($row = mysql_fetch_row( $result ))
				$func($row[0]);

			mysql_free_result( $result );
		}

		return true;
	}

	/**
	 * Short description for 'delete_profile'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $user Parameter description (if any) ...
	 * @param      string $storage Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function delete_profile($user, $storage)
	{
		if (!empty($storage) && !in_array($storage,array('mysql','ldap')))
			return false;

		$mconfig = & JComponentHelper::getParams( 'com_members' );
		$ldapProfileMirror = $mconfig->get('ldapProfileMirror');

		if (empty($storage))
			$storage = ($ldapProfileMirror) ? 'all' : 'mysql';

		ximport('Hubzero_User_Profile');
		$profile = new Hubzero_User_Profile();

		if ($storage == 'mysql' || $storage == 'all')
		{
			$profile->load($user,'mysql');
			$profile->delete('mysql');
		}

		if ($storage == 'ldap' || $storage == 'all')
		{
			$profile->load($user,'ldap');
			$profile->delete('ldap');
		}
	}

	/**
	 * Short description for 'find_by_email'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $email Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function find_by_email($email)
	{
		if (empty($email))
			return false;

		$db = &JFactory::getDBO();

		$query = "SELECT username FROM #__xprofiles WHERE email=" . $db->Quote($email);

		$db->setQuery($query);

		$result = $db->loadResultArray();

		if (empty($result))
			return false;

		return $result;
	}

	/**
	 * Short description for 'getMemberPhoto'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      mixed $member Parameter description (if any) ...
	 * @param      integer $anonymous Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function getMemberPhoto($member, $anonymous=0, $thumbit=true)
	{
		$config =& JComponentHelper::getParams('com_members');

		$thumb = '';
		if (!$anonymous && $member->get('picture')) 
		{
			$thumb .= DS . trim($config->get('webpath'), DS);
			$thumb .= DS . Hubzero_User_Profile_Helper::niceidformat($member->get('uidNumber'));
			$thumb .= DS . ltrim($member->get('picture'), DS);

			if ($thumbit)
			{
				$thumb = Hubzero_User_Profile_Helper::thumbit($thumb);
			}
		}

		$dfthumb = DS . trim($config->get('defaultpic'), DS);
		if ($thumbit)
		{
			$dfthumb = Hubzero_User_Profile_Helper::thumbit($dfthumb);
		}

		if ($thumb && is_file(JPATH_ROOT . $thumb)) 
		{
			return $thumb;
		} 
		else if (is_file(JPATH_ROOT . $dfthumb)) 
		{
			return $dfthumb;
		}
	}

	/**
	 * Short description for 'thumbit'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $thumb Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public function thumbit($thumb)
	{
		$image = explode('.',$thumb);
		$n = count($image);
		$image[$n-2] .= '_thumb';
		$end = array_pop($image);
		$image[] = $end;
		$thumb = implode('.',$image);

		return $thumb;
	}

	/**
	 * Short description for 'niceidformat'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      integer $someid Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */
	public function niceidformat($someid)
	{
		$prfx = '';
		if (substr($someid, 0, 1) == '-')
		{
			$prfx = 'n';
			$someid = substr($someid, 1);
		}
		while (strlen($someid) < 5)
		{
			$someid = 0 . "$someid";
		}
		return $prfx . $someid;
	}
}

