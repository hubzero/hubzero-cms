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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Projects autocompleter helper class for users and groups
 */
class AutocompleteHandler extends JObject
{
	/**
	 * _getAutocomplete
	 * 
	 * @param      array $filters
	 * @param      string $which
	 * @param      object $database
	 * @param      integer $uid
	 * @return     object
	 */
	public function _getAutocomplete( $filters=array(), $which = 'user', $database, $uid ) 
	{
			if ($which == 'user') 
			{
					$query = "SELECT x.username, x.uidNumber, ";
					$query .= " CASE WHEN x.surname IS NOT NULL AND x.surname != '' AND x.surname != '&nbsp;' AND x.givenName IS NOT NULL AND x.givenName != '' AND x.givenName != '&bnsp;' THEN
					   CONCAT(x.givenName, COALESCE(CONCAT(' ', x.middleName), '') , x.surname)
					ELSE
					   COALESCE(x.name, '')
					END AS fullname ";
					$query .= " FROM #__xprofiles as x ";
					$query .= "
						WHERE x.uidNumber > 0 AND ((LOWER( x.name ) LIKE '%".$filters['search']."%' )
						OR (LOWER( x.username ) LIKE '".$filters['search']."%' )
						OR (LOWER( x.email ) LIKE '".$filters['search']."%' ))
						ORDER BY x.username ASC";
			}
			elseif ($which == 'publicgroup') 
			{
					$query = "SELECT t.gidNumber, t.cn, t.description ";
					$query .= " FROM #__xgroups AS t ";
					$query .= "WHERE t.type=1 AND
						(LOWER( t.description ) LIKE '%".$filters['search']."%' )
						ORDER BY t.description ASC";
			}
			else 
			{
					$query = "SELECT t.gidNumber, t.cn, t.description ";
					$query .= " FROM #__xgroups AS t, #__xgroups_members AS m ";
					$query .= "
						WHERE t.type=1 AND m.gidNumber=t.gidNumber AND m.uidNumber=".$uid." AND
						(LOWER( t.description ) LIKE '%".$filters['search']."%' )
						ORDER BY t.description ASC";
			}
	
			$database->setQuery( $query );
			return $database->loadObjectList();
	}
	
	/**
	 * Get collaborators from all projects
	 * 
	 * @param      integer $uid
	 * @param      object $database
	 * @param      string $selector
	 * @return     array
	 */
	public function _getCollaborators( $uid, $database, $selector = 'uidNumber' ) {
		
		$collaborators = array();
		$usergroups = AutocompleteHandler::_getGroups( $uid, $database );
		if (empty($usergroups)) 
		{
			$where_groups = ' WHERE 1=2 ';
		}
		else 
		{
			$where_groups = ' WHERE m.gidNumber IN ( ';
			$k = 1;
			foreach ($usergroups as $ug) 
			{
				$where_groups .= $ug->gidNumber;
				$where_groups .= $k == count($usergroups) ? '' : ',';
				$k++;
			}
			$where_groups .= ' ) AND m.uidNumber!=' . $uid . ' ';
		}
		
		$query  = "(SELECT DISTINCT x.$selector ";
		$query .= " FROM #__xprofiles as x JOIN #__xgroups_members AS m ON m.uidNumber=x.uidNumber ";
		$query .= $where_groups." ) ";
		$query .= " UNION ";
		$query .= "(SELECT DISTINCT x.$selector ";
		$query .= " FROM #__xprofiles as x JOIN #__xgroups_managers AS m ON m.uidNumber=x.uidNumber ";
		$query .= $where_groups . " ) ";
		
		$database->setQuery( $query );
		$result = $database->loadObjectList();
		$collaborators = $result ? $result[0] : $collaborators;
		
		return $collaborators;
	}
	
	/**
	 * Get groups user belongs to
	 * 
	 * @param      integer $uid
	 * @param      object $database
	 * @param      string $selector
	 * @return     array
	 */
	public function _getGroups( $uid, $database, $selector = "gidNumber" )
	{

		// Get all groups the user is a member of
		$query1 = "SELECT DISTINCT g.$selector FROM #__xgroups AS g, #__xgroups_members AS m WHERE g.type='1' AND m.gidNumber=g.gidNumber AND m.uidNumber=".$uid;
		$query2 = "SELECT DISTINCT g.$selector FROM #__xgroups AS g, #__xgroups_managers AS m WHERE g.type='1' AND m.gidNumber=g.gidNumber AND m.uidNumber=".$uid;
		
		$query = "( $query1 ) UNION ( $query2 )";
		
		$database->setQuery($query);
		$result = $database->loadObjectList();

		if (empty($result))
			return array();

		return $result;
	}
	
	/**
	 * Get projects user belong to
	 * 
	 * @param      integer $uid
	 * @param      object $database
	 * @param      string $selector
	 * @return     array
	 */
	public function _getProjects( $uid, $database, $selector = "id" )
	{
		// Get all groups the user is a member of
		$query = "SELECT DISTINCT p.$selector FROM #__projects AS p, #__project_owners AS o WHERE o.type='1' AND p.id=o.projectid AND o.userid=".$uid;
	
		$database->setQuery($query);
		$result = $database->loadObjectList();

		if (empty($result))
			return array();

		return $result;
	}
}
