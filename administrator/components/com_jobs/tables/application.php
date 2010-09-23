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


class JobApplication extends JTable
{
	var $id         = NULL;  // @var int(11) Primary key
	var $jid		= NULL;  // @var int(11)
	var $uid		= NULL;  // @var int(11)
	var $applied	= NULL;
	var $withdrawn	= NULL;
	var $cover		= NULL;
	var $resumeid	= NULL;
	var $status		= NULL;
	var $reason		= NULL;
		
	//-----------
	
	public function __construct( &$db ) 
	{
		parent::__construct( '#__jobs_applications', 'id', $db );
	}
	
	//----------
	 
	public function getApplications ($jobid) 
	{	 	
		if ($jobid === NULL) {
			return false;
		}
		
		$sql = "SELECT a.* FROM  #__jobs_applications AS a ";
		$sql.= "JOIN #__jobs_seekers as s ON s.uid=a.uid";
		$sql.= "\n WHERE  a.jid='$jobid' AND s.active=1 ";
		$sql.= " ORDER BY a.applied DESC";
		
		$this->_db->setQuery( $sql );
		return $this->_db->loadObjectList();	 
	}
	
	//--------
	
	public function loadApplication( $uid = NULL, $jid = NULL, $jobcode = NULL )
	{		
		if ($uid === NULL or ($jid === NULL && $jobcode === NULL)) {
			return false;
		}
		
		$query  = "SELECT * FROM $this->_tbl as A ";
		$query .= $jid ? "" : " JOIN #__jobs_openings as J ON J.id=A.jid ";
		$query .= " WHERE A.uid='$uid' ";
		$query .=  $jid ? "AND A.jid='$jid' " : "AND J.code='$jobcode' ";
		$query .= " LIMIT 1";
		$this->_db->setQuery( $query );
		if ($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
			return false;
		}
	}	
}
