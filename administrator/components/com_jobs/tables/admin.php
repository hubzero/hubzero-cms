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

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Short description for 'JobAdmin'
 * 
 * Long description (if any) ...
 */
class JobAdmin extends JTable
{

	/**
	 * Description for 'id'
	 * 
	 * @var unknown
	 */
	var $id         = NULL;  // @var int(11) Primary key


	/**
	 * Description for 'jid'
	 * 
	 * @var unknown
	 */
	var $jid		= NULL;  // @var int(11)


	/**
	 * Description for 'uid'
	 * 
	 * @var unknown
	 */
	var $uid		= NULL;  // @var int(11)

	//-----------


	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$db Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__jobs_admins', 'id', $db );
	}

	/**
	 * Short description for 'isAdmin'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $uid Parameter description (if any) ...
	 * @param      string $jid Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function isAdmin($uid,  $jid)
	{
		if ($uid === NULL or $jid === NULL) {
			return false;
		}

		$query  = "SELECT id ";
		$query .= "FROM #__jobs_admins  ";
		$query .= "WHERE uid = '".$uid."' AND jid = '".$jid."'";
		$this->_db->setQuery( $query );
		if ($this->_db->loadResult()) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Short description for 'getAdmins'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $jid Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function getAdmins($jid)
	{
		if ($jid === NULL) {
			return false;
		}

		$admins = array();

		$query  = "SELECT uid ";
		$query .= "FROM #__jobs_admins  ";
		$query .= "WHERE jid = '".$jid."'";
		$this->_db->setQuery( $query );
		$result = $this->_db->loadObjectList();
		if ($result) {
			foreach ($result as $r)
			{
				$admins[] = $r->uid;
			}
		}

		return $admins;
	}
}

