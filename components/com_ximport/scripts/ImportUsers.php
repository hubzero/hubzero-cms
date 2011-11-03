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

class ImportUsers extends XImportHelperScript
{
	protected $_description = 'Import user profiles from LDAP.';

	public function run()
	{
        echo "import users...<br />";

        $query = "SELECT username FROM #__users;";

        $this->_db->setQuery($query);

        $result = $this->_db->query();

        if ($result === false)
        {
            echo 'Error retrieving data from juser table: ' . $this->_db->getErrorMsg();
            return false;
        }

        while ($row = mysql_fetch_assoc( $result ))
            $this->_importUser($row['username']);

        mysql_free_result( $result );
	}

	private function _importUser($name)
	{
		$profile = new Hubzero_User_Profile();
    	$profile->load($name,'ldap');

    	$result = $profile->create('mysql');

    	if ($result === false)
            	echo "Error importing $name<br />";
    	else
            	echo "Imported $name<br />";
	}
}
