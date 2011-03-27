<?php
/**
 * @package     hubzero-cms
 * @author      Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright   Copyright 2009-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 *
 * Copyright 2009-2011 Purdue University. All rights reserved.
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
defined('_JEXEC') or die('Restricted access');

class Hubzero_Ldap
{
	static public function user_exists($username = '')
	{
                $xhub = &Hubzero_Factory::getHub();
                $conn = &Hubzero_Factory::getPLDC();

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

