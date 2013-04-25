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
 * Table class for project log history
 */
class ProjectStats extends JTable 
{
	/**
	 * int(11) Primary key
	 * 
	 * @var integer
	 */
	var $id         	= NULL;
	
	/**
	 * Datetime (0000-00-00 00:00:00)
	 * 
	 * @var datetime
	 */
	var $processed		= NULL;
			
	/**
	 * Month 
	 * 
	 * @var integer
	 */	
	var $month       	= NULL;
	
	/**
	 * Year
	 * 
	 * @var integer
	 */	
	var $year       	= NULL;
	
	/**
	 * Week
	 * 
	 * @var integer
	 */	
	var $week       	= NULL;
				
	/**
	 * Dump of all stats
	 * 
	 * @var text
	 */	
	var $stats       	= NULL;	
	
	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct( &$db ) 
	{
		parent::__construct( '#__project_stats', 'id', $db );
	}
	
	/**
	 * Load item
	 * 
	 * @param      integer 	$projectid		Project ID
	 * @return     mixed False if error, Object on success
	 */	
	public function loadLog ( $year = NULL, $month = NULL, $week = '' ) 
	{
		if (!$year && !$month && !$week)
		{
			return false;
		}
		
		$query  = "SELECT * FROM $this->_tbl WHERE 1=1 ";
		$query .= $year ? " AND year='". $year ."' " : '';
		$query .= $month ? " AND month='". $month ."' " : '';
		$query .= $week ? " AND week='". $week ."' " : '';
		$query .= " ORDER BY processed DESC LIMIT 1";
		
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
