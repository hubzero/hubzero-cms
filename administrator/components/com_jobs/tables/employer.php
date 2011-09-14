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
 * Short description for 'Employer'
 * 
 * Long description (if any) ...
 */
class Employer extends JTable
{

	/**
	 * Description for 'id'
	 * 
	 * @var unknown
	 */
	var $id         		= NULL;  // @var int(11) Primary key


	/**
	 * Description for 'uid'
	 * 
	 * @var unknown
	 */
	var $uid				= NULL;  // @var int(11)


	/**
	 * Description for 'added'
	 * 
	 * @var unknown
	 */
	var $added    			= NULL;  // @var datetime (0000-00-00 00:00:00)


	/**
	 * Description for 'subscriptionid'
	 * 
	 * @var unknown
	 */
	var $subscriptionid		= NULL;  // @var int(11)


	/**
	 * Description for 'companyName'
	 * 
	 * @var unknown
	 */
	var $companyName		= NULL;  // @var varchar (250)


	/**
	 * Description for 'companyLocation'
	 * 
	 * @var unknown
	 */
	var $companyLocation	= NULL;  // @var varchar (250)


	/**
	 * Description for 'companyWebsite'
	 * 
	 * @var unknown
	 */
	var $companyWebsite		= NULL;  // @var varchar (250)

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
		parent::__construct( '#__jobs_employers', 'id', $db );
	}

	/**
	 * Short description for 'isEmployer'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $uid Parameter description (if any) ...
	 * @param      integer $admin Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function isEmployer($uid, $admin=0)
	{
		if ($uid === NULL) {
			return false;
		}

		$now = date( 'Y-m-d H:i:s', time() );
		$query  = "SELECT e.id ";
		$query .= "FROM #__jobs_employers AS e  ";
		if (!$admin) {
			$query .= "JOIN #__users_points_subscriptions AS s ON s.id=e.subscriptionid AND s.uid=e.uid ";
			$query .= "WHERE e.uid = '".$uid."' AND s.status=1";
			$query .= " AND s.expires > '".$now."' ";
		} else {
			$query .= "WHERE e.uid = 1";
		}
		$this->_db->setQuery( $query );
		if ($this->_db->loadResult()) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Short description for 'loadEmployer'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $uid Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function loadEmployer( $uid=NULL )
	{
		if ($uid === NULL) {
			return false;
		}

		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE uid='$uid' " );
		if ($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
			return false;
		}
	}

	/**
	 * Short description for 'getEmployer'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $uid Parameter description (if any) ...
	 * @param      string $subscriptioncode Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function getEmployer( $uid = NULL, $subscriptioncode = NULL )
	{
		if ($uid === NULL or $subscriptioncode === NULL) {
			return false;
		}
		$query  = "SELECT * ";
		$query .= "FROM #__jobs_employers AS e  ";
		if ($subscriptioncode == 'admin') {
			$query .= "WHERE e.uid = 1";
		} else if ($subscriptioncode) {
			$query .= "JOIN #__users_points_subscriptions AS s ON s.id=e.subscriptionid AND s.uid=e.uid ";
			$query .= "WHERE s.code='$subscriptioncode'";
		} else if ($uid) {
			$query .= "WHERE e.uid = '".$uid."'";
		}
		$this->_db->setQuery( $query);
		$result = $this->_db->loadObjectList();
		if ($result) {
			return $result[0];
		} else {
			return false;
		}
	}
}

