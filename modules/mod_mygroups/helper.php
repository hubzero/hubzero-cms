<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 * All rights reserved.
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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for 'modMyGroups'
 * 
 * Long description (if any) ...
 */
class modMyGroups
{

	/**
	 * Description for 'attributes'
	 * 
	 * @var array
	 */
	private $attributes = array();

	//-----------


	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $params Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct( $params )
	{
		$this->params = $params;
	}

	//-----------


	/**
	 * Short description for '__set'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $property Parameter description (if any) ...
	 * @param      unknown $value Parameter description (if any) ...
	 * @return     void
	 */
	public function __set($property, $value)
	{
		$this->attributes[$property] = $value;
	}

	//-----------


	/**
	 * Short description for '__get'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $property Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function __get($property)
	{
		if (isset($this->attributes[$property])) {
			return $this->attributes[$property];
		}
	}

	//-----------


	/**
	 * Short description for '_getGroups'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $uid Parameter description (if any) ...
	 * @param      string $type Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	private function _getGroups($uid, $type='all')
	{
		$db =& JFactory::getDBO();

		// Get all groups the user is a member of
		$query1 = "SELECT g.published, g.description, g.cn, '1' AS registered, '0' AS regconfirmed, '0' AS manager 
				   FROM #__xgroups AS g, #__xgroups_applicants AS m 
				   WHERE (g.type='1' || g.type='3') AND m.gidNumber=g.gidNumber AND m.uidNumber=".$uid;
		
		$query2 = "SELECT g.published, g.description, g.cn, '1' AS registered, '1' AS regconfirmed, '0' AS manager 
				   FROM #__xgroups AS g, #__xgroups_members AS m 
				   WHERE (g.type='1' || g.type='3') AND m.uidNumber NOT IN 
				   		(SELECT uidNumber 
						 FROM #__xgroups_managers AS manager
						 WHERE manager.gidNumber = m.gidNumber)
				   AND m.gidNumber=g.gidNumber AND m.uidNumber=".$uid;
		
		$query3 = "SELECT g.published, g.description, g.cn, '1' AS registered, '1' AS regconfirmed, '1' AS manager 
				   FROM #__xgroups AS g, #__xgroups_managers AS m 
				   WHERE (g.type='1' || g.type='3') AND m.gidNumber=g.gidNumber AND m.uidNumber=".$uid;
		
		$query4 = "SELECT g.published, g.description, g.cn, '0' AS registered, '1' AS regconfirmed, '0' AS manager 
				   FROM #__xgroups AS g, #__xgroups_invitees AS m 
				   WHERE (g.type='1' || g.type='3') AND m.gidNumber=g.gidNumber AND m.uidNumber=".$uid;

		switch ($type)
		{
			case 'all':
				$query = "( $query1 ) UNION ( $query2 ) UNION ( $query3 ) UNION ( $query4 ) ORDER BY cn ASC";
			break;
			case 'applicants':
				$query = $query1;
			break;
			case 'members':
				$query = $query2;
			break;
			case 'managers':
				$query = $query3;
			break;
			case 'invitees':
				$query = $query4;
			break;
		}

		$db->setQuery($query);
		$db->query();

		$result = $db->loadObjectList();

		if (empty($result))
			return array();

		return $result;
	}

	//-----------


	/**
	 * Short description for 'getStatus'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      object $group Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function getStatus( $group )
	{
		if ($group->manager) {
			$status = 'manager';
		} else {
			if ($group->registered) {
				if ($group->regconfirmed) {
					$status = 'member';
				} else {
					$status = 'pending';
				}
			} else {
				if ($group->regconfirmed) {
					$status = 'invitee';
				} else {
					$status = '';
				}
			}
		}
		return $status;
	}

	//-----------


	/**
	 * Short description for 'display'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	public function display()
	{
		$juser =& JFactory::getUser();

		// Get the module parameters
		$params =& $this->params;
		$this->moduleclass = $params->get( 'moduleclass' );
		$limit = intval( $params->get( 'limit' ) );
		$limit = ($limit) ? $limit : 10;

		// Get the user's groups
		$members  = $this->_getGroups( $juser->get('id'), 'all' );

		foreach ($members as $mem) 
		{
			$groups[] = $mem;
		}

		$this->limit = $limit;
		$this->groups = $groups;

		// Push the module CSS to the template
		ximport('Hubzero_Document');
		Hubzero_Document::addModuleStyleSheet('mod_mygroups');
	}
}

