<?php
/**
 * @package		HUBzero CMS
 * @author		Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright	Copyright 2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2009 by Purdue Research Foundation, West Lafayette, IN 47906.
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
defined('_JEXEC') or die('Restricted access');

class Hubzero_Ldap
{
	static public function user_exists($username = '')
	{
                $xhub = &XFactory::getHub();
                $conn = &XFactory::getPLDC();

                if ( empty($username))
                        return false;

                $hubLDAPBaseDN = $xhub->getCfg('hubLDAPBaseDN');

                if (empty($hubLDAPBaseDN))
                        return false;

                if (!is_numeric($username))
                {
                        $dn = "uid=$username,ou=users," . $hubLDAPBaseDN;
                        $filter = '(objectclass=*)';
                }
                else
                {
                        $dn = 'ou=users,' . $hubLDAPBaseDN;
                        $filter = '(uidNumber=' . $username . ')';
                }

                $attributes[] = 'sn';
                $attributes[] = 'member';

                $sr = @ldap_search($conn, $dn, $filter, $attributes, 0, 1, 0, 3);

                if ($sr === false)
                        return false;

                $count = @ldap_count_entries($conn, $sr);

                if ($count === false)
                        return false;

                if ($count != 1)
                        return false;

                $entry = @ldap_first_entry($conn, $sr);

                if ($entry === false)
                     return false;

                $attributes = ldap_get_attributes($conn, $entry);

                if ($attributes === false)
                        return false;

		return true;
	}
}
