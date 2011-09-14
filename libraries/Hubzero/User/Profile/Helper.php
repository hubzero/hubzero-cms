<?php
/**
 * @package     hubzero-cms
 * @author      Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class Hubzero_User_Profile_Helper
{
	public function iterate_profiles($func, $storage)
	{
		$db = &JFactory::getDBO();

		if (!empty($storage) && !in_array($storage,array('mysql','ldap')))
			return false;

		if ($storage == 'ldap')
		{
			$xhub = &Hubzero_Factory::getHub();
			$conn = &Hubzero_Factory::getPLDC();

			$hubLDAPBaseDN = $xhub->getCfg('hubLDAPBaseDN');

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

	public function getMemberPhoto( $member, $anonymous=0 )
	{
		$config =& JComponentHelper::getParams( 'com_members' );

		if (!$anonymous && $member->get('picture')) {
			$thumb  = $config->get('webpath');
			if (substr($thumb, 0, 1) != DS) {
				$thumb = DS.$thumb;
			}
			if (substr($thumb, -1, 1) == DS) {
				$thumb = substr($thumb, 0, (strlen($thumb) - 1));
			}
			$thumb .= DS.Hubzero_User_Profile_Helper::niceidformat($member->get('uidNumber')).DS.$member->get('picture');

			$thumb = Hubzero_User_Profile_Helper::thumbit($thumb);
		} else {
			$thumb = '';
		}

		$dfthumb = $config->get('defaultpic');
		if (substr($dfthumb, 0, 1) != DS) {
			$dfthumb = DS.$dfthumb;
		}
		$dfthumb = Hubzero_User_Profile_Helper::thumbit($dfthumb);

		if ($thumb && is_file(JPATH_ROOT.$thumb)) {
			return $thumb;
		} else if (is_file(JPATH_ROOT.$dfthumb)) {
			return $dfthumb;
		}
	}

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

	public function niceidformat($someid)
	{
		while (strlen($someid) < 5)
		{
			$someid = 0 . "$someid";
		}
		return $someid;
	}
}

