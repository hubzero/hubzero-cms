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
}
