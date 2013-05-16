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
 * Table class for publication access logs
 */
class PublicationLog extends JTable 
{
	/**
	 * int(11) Primary key
	 * 
	 * @var integer
	 */
	var $id         			= NULL;
	
	/**
	 * int(11) Publication ID
	 * 
	 * @var integer
	 */
	var $publication_id 		= NULL;
	
	/**
	 * int(11) Publication version ID
	 * 
	 * @var integer
	 */
	var $publication_version_id = NULL;
	
	/**
	 * int(11) Number of page views
	 * 
	 * @var integer
	 */
	var $page_views 			= NULL;
	
	/**
	 * int(11) Number of accesses (clicks on the black button)
	 * 
	 * @var integer
	 */
	var $primary_accesses 		= NULL;
	
	/**
	 * int(11) Number of accesses of supporting docs (clicks on the black button)
	 * 
	 * @var integer
	 */
	var $support_accesses 		= NULL;
	
	/**
	 * Datetime (0000-00-00 00:00:00)
	 * 
	 * @var datetime
	 */
	var $modified				= NULL;
			
	/**
	 * Month 
	 * 
	 * @var integer
	 */	
	var $month       			= NULL;
	
	/**
	 * Year
	 * 
	 * @var integer
	 */	
	var $year       			= NULL;
								
	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct( &$db ) 
	{
		parent::__construct( '#__publication_logs', 'id', $db );
	}
	
	/**
	 * Log access
	 * 
	 * @param      integer 	$pid		Publication ID
	 * @param      integer 	$vid		Publication version ID
	 * @param      string 	$type		view, primary or support
	 * @return     void
	 */	
	public function logAccess ( $pid = NULL, $vid = NULL, $type = 'view' ) 
	{
		if (!$pid || !$vid)
		{
			return false;
		}
		
		$thisYearNum 	= date('y', time());
		$thisMonthNum 	= date('m', time());
		
		// Load record to update
		if (!$this->loadMonthLog( $pid, $vid ))
		{
			$this->publication_id 			= $pid;
			$this->publication_version_id 	= $vid;
			$this->modified					= date( 'Y-m-d H:i:s' );
			$this->year						= $thisYearNum;
			$this->month					= $thisMonthNum;			
		}
		
		if ($type == 'primary')
		{
			$this->primary_accesses = $this->primary_accesses + 1;
		}
		elseif ($type == 'support')
		{
			$this->support_accesses = $this->support_accesses + 1;
		}
		else
		{
			$this->page_views = $this->page_views + 1;
		}
		
		if ($this->store())
		{
			return true;
		}

		return false;
	}
	
	/**
	 * Load log
	 * 
	 * @param      integer 	$pid		Publication ID
	 * @param      integer 	$vid		Publication version ID
	 * @return     void
	 */	
	public function loadMonthLog ( $pid = NULL, $vid = NULL ) 
	{
		if (!$pid || !$vid)
		{
			return false;
		}
		
		$thisYearNum 	= date('y', time());
		$thisMonthNum 	= date('m', time());
		
		// First select this month's log if exists		
		$query  = "SELECT * FROM $this->_tbl WHERE publication_id=$pid ";
		$query .= "AND publication_version_id=$vid ";
		$query .= "AND year='$thisYearNum' AND month='$thisMonthNum' ";
		$query .= "ORDER BY modified DESC LIMIT 1";
		
		$this->_db->setQuery( $query );
		if ($result = $this->_db->loadAssoc()) 
		{
			return $this->bind( $result );
		} 
		else 
		{
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}		
	}
}
