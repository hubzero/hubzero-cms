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

/****************************

THIS IS A WORK IN PROGRESS. NOT READY FOR USE.

DO NOT USE

DO NOT USE

DO NOT USE


******************************/

class XGroupHelper
{
	function get_user_groups($login)
	{
 		$xhub          =& XFactory::getHub();
        $hubLDAPBaseDN =  $xhub->getCfg('hubLDAPBaseDN');
        $ldapconn      =& XFactory::getPLDC();
        $dn            =  'ou=groups,' . $hubLDAPBaseDN;

		if (!$ldapconn)
			return false;

		$request[] = 'gid';
		$request[] = 'groupName';
		$request[] = 'description';
		$request[] = 'public';

		foreach(array('applicant','member','owner') as $type)
		{
			$filter = '(&(objectclass=hubGroup)(' . $type . '=uid=' . $login . ',ou=users,' . $hubLDAPBaseDN . '))';

			$sr = ldap_search($ldapconn, $dn, $filter, $request, 0, 0, 0, 3);

			$entry = ldap_first_entry($ldapconn, $sr);

			while($entry)
			{
				$attributes = ldap_get_attributes($ldapconn, $entry);

				$group = array();
				$group['gid'] = isset($attributes['gid'][0]) ? $attributes['gid'][0] : null;
				$group['name'] = isset($attributes['groupName'][0]) ? $attributes['groupName'][0] : null;
				$group['description'] = isset($attributes['description'][0]) ? $attributes['description'][0] : null;
				$group['confirmed'] = isset($attributes['public'][0]) ? $attributes['public'][0] : null;
				$group['manager'] = ($type == 'owner') ? 1 : 0;
				$group['regconfirmed'] = ($type == 'member') ? 1 : 0;
				$result[] = $group;

				$entry = ldap_next_entry($ldapconn, $entry);	
			}
		}

		if (!isset($result))
			return false;

		return $result;
	}
}
