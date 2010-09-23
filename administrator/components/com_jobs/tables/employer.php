<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
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

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );


class Employer extends JTable
{
	var $id         		= NULL;  // @var int(11) Primary key
	var $uid				= NULL;  // @var int(11)
	var $added    			= NULL;  // @var datetime (0000-00-00 00:00:00)
	var $subscriptionid		= NULL;  // @var int(11)
	var $companyName		= NULL;  // @var varchar (250)
	var $companyLocation	= NULL;  // @var varchar (250)
	var $companyWebsite		= NULL;  // @var varchar (250)
	
	//-----------
	
	public function __construct( &$db ) 
	{
		parent::__construct( '#__jobs_employers', 'id', $db );
	}
	
	//-----------
	
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
		
	//--------
	
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
	
	//--------
	
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
