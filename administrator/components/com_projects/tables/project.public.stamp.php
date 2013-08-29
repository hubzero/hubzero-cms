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
 * Table class for project public links
 */
class ProjectPubStamp extends JTable 
{
	/**
	 * int(11) Primary key
	 * 
	 * @var integer
	 */
	var $id         	= NULL;
	
	/**
	 * Project ID
	 * 
	 * @var int
	 */
	var $projectid      = NULL;
	
	/**
	 * Is link listed on public page?
	 * 
	 * @var tinyint
	 */
	var $listed      	= NULL;
	
	/**
	 * Stamp
	 * 
	 * @var string
	 */
	var $stamp        	= NULL;
	
	/**
	 * Load method
	 * 
	 * @var string
	 */
	var $method        	= NULL;
	
	/**
	 * Reference type
	 * 
	 * @var string
	 */
	var $type        	= NULL;
	
	/**
	 * Reference url
	 * 
	 * @var string
	 */
	var $reference      = NULL;	
	
	/**
	 * Datetime (0000-00-00 00:00:00)
	 * 
	 * @var datetime
	 */
	var $expires		= NULL;
	
	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $created_by					= NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 * 
	 * @var string
	 */
	var $created					= NULL;
	
	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct( &$db ) 
	{
		parent::__construct( '#__project_public_stamps', 'id', $db );
	}
	
	/**
	 * Load item
	 * 
	 * @param      integer 	$projectid		Project ID
	 * @return     mixed False if error, Object on success
	 */	
	public function loadItem ( $stamp = '') 
	{
		if (!$stamp)
		{
			return false;
		}
		$now = date('Y-m-d H:i:s');
		
		$query  = "SELECT * FROM $this->_tbl WHERE stamp='$stamp' ";
		$query .= " AND (expires IS NULL OR expires <= '$now')";
		$query .= " LIMIT 1";
		
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
	
	/**
	 * Get listed items
	 * 
	 * @param      int		$projectid		Project ID
	 * @param      string 	$type			
	 * @return     object array
	 */	
	public function getPubList ( $projectid = 0, $type = '') 
	{
		if (!$projectid)
		{
			return false;
		}
		
		$query  = "SELECT * FROM $this->_tbl WHERE projectid=$projectid ";
		$query .= "AND type='" . $type . "' AND listed=1 ORDER BY created DESC ";
		
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();		
	}
		
	/**
	 * Check if stamp exists
	 * 
	 * @param      integer 	$projectid		Project ID
	 * @param      string 	$reference		Reference string to object (JSON)
	 * @return     mixed False if error, Object on success
	 */	
	public function checkStamp ( $projectid = 0, $reference = '', $type = '') 
	{
		if (!$projectid || !$reference)
		{
			return false;
		}
						
		$query  = "SELECT * FROM $this->_tbl WHERE projectid=$projectid ";
		$query .= "AND reference='" . mysql_real_escape_string($reference) . "' AND type='$type' LIMIT 1";
		
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
	
	/**
	 * Register stamp
	 * 
	 * @param      integer 	$projectid		Project ID
	 * @param      string 	$reference		Reference string to object (JSON)
	 * @return     mixed False if error, Object on success
	 */	
	public function registerStamp ( $projectid = 0, $reference = '', $type = 'files', $listed = NULL, $expires = NULL) 
	{
		if (!$projectid || !$reference)
		{
			return false;
		}
		
		// Load record
		if ($this->checkStamp( $projectid, $reference, $type ))
		{
			if ($listed === NULL && $expires === NULL)
			{
				return true;
			}
			
			// These values may be updated
			$this->listed	= $listed === NULL ? $this->listed : $listed;	
			$this->expires	= $expires === NULL ? $this->expires : $expires;
			
			if ($this->store())
			{
				return true;
			}						
		}
		else
		{
			$this->projectid 	= $projectid;
			$this->reference 	= $reference;
			$this->type			= $type;
			$this->created 		= date('Y-m-d H:i:s');
			
			$juser =& JFactory::getUser();
			$this->created_by	= $juser->get('id');
			
			$this->listed		= $listed;	
			$this->expires		= $expires;
			
			require_once( JPATH_ROOT . DS . 'components' . DS .'com_projects' . DS . 'helpers' . DS . 'html.php');
			
			// Generate stamp
			$stamp = ProjectsHtml::generateCode (20, 20, 0, 1, 1);
			
			$this->stamp 		= $stamp;
			
			if ($this->store())
			{
				return true;
			}			
		}
		
		return false;		
	}
}
