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
 * Table class for project updates
 */
class ProjectMicroblog extends JTable 
{
	/**
	 * int(11) Primary key
	 * 
	 * @var integer
	 */
	var $id         		= NULL;

	/**
	 * Entry, varchar(255)
	 * 
	 * @var string
	 */	
	var $blogentry       	= NULL;
		
	/**
	 * Datetime (0000-00-00 00:00:00)
	 * 
	 * @var datetime
	 */
	var $posted			= NULL;

	/**
	 * User id of creator
	 * 
	 * @var integer
	 */	
	var $posted_by       	= NULL;
	
	/**
	 * State
	 * 
	 * @var integer
	 */	
	var $state       		= NULL;
	
	/**
	 * Params
	 * 
	 * @var text
	 */	
	var $params       		= NULL;
	
	/**
	 * Project id
	 * 
	 * @var integer
	 */	
	var $projectid       	= NULL;

	/**
	 * Show to managers only?
	 * 
	 * @var integer
	 */	
	var $managers_only      = NULL;

	/**
	 * Activity ID
	 * 
	 * @var integer
	 */	
	var $activityid       	= NULL;
	
	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__project_microblog', 'id', $db );
	}
	
	/**
	 * Validate data
	 * 
	 * @return     boolean True if data is valid
	 */
	public function check() 
	{
		if (!$this->projectid) 
		{
			$this->setError( JText::_('Missing project ID.') );
			return false;
		}
		if (trim( $this->blogentry ) == '') 
		{
			$this->setError( JText::_('Please provide content.') );
			return false;
		}
		if (!$this->posted_by) 
		{
			$this->setError( JText::_('Missing creator ID.') );
			return false;
		}
		return true;
	}
			
	/**
	 * Get items
	 * 
	 * @param      integer $projectid
	 * @param      array $filters
	 * @param      integer $id
	 * @return     object
	 */
	public function getEntries($projectid = NULL, $filters=array(), $id = 0) 
	{
		if ($projectid === NULL) 
		{
			return false;
		}
		$pc = new ProjectComment( $this->_db );
		
		$query = "SELECT m.*, (SELECT COUNT(*) FROM ".$pc->getTableName()." AS c 
			WHERE c.itemid=m.id AND c.tbl='blog') 
			AS comments, u.name ".$this->_buildQuery( $projectid, $filters, $id );
		
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
	
	/**
	 * Check if identical entry is made (prevents duplicates on multiple 'save' click)
	 * 
	 * @param      integer $uid
	 * @param      integer $projectid
	 * @param      string $entry
	 * @param      string $today
	 * @return     integer or NULL
	 */
	public function checkDuplicate($uid, $projectid, $entry, $today) 
	{
		$query = "SELECT id FROM $this->_tbl WHERE posted_by=$uid 
			AND projectid=$projectid AND blogentry='$entry' 
			AND posted  LIKE '$today%' ";
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
	
	/**
	 * Build query
	 * 
	 * @param      integer $projectid
	 * @param      array $filters
	 * @param      integer $id
	 * @return     string
	 */
	private function _buildQuery($projectid, $filters, $id) 
	{		
		$query  = "FROM $this->_tbl AS m,
					#__users AS u  
					WHERE m.projectid='".$projectid."' AND m.posted_by=u.id ";
					
		if ($id) 
		{
			$query .= " AND m.id=".$id;
		}
		else 
		{
			if (isset($filters['posted_by']) && $filters['posted_by'] != 0) 
			{
				$query .= " AND m.posted_by=".$filters['posted_by'];
			}
			if (isset($filters['managers_only']) && $filters['managers_only'] != 0) 
			{
				$query .= " AND m.managers_only=1";
			}
			if (isset($filters['activityid']) && $filters['activityid'] != 0) 
			{
				$query .= " AND m.activityid=".$filters['activityid'];
			}
			if (isset($filters['search']) && $filters['search'] != '') 
			{
				$filters['search'] = strtolower(stripslashes($filters['search']));
				$query .= " AND (LOWER(m.blogentry) LIKE '%".$filters['search']."%')";
			}
		}	
		$query .= " AND m.state != 2";
		if (isset($filters['order']) && $filters['order'] != '') 
		{
			$query .= " ORDER BY ".$filters['order'];
		} 
		else 
		{
			$query .= " ORDER BY m.posted DESC";
		}
		if (isset($filters['limit']) && $filters['limit'] != 0) 
		{
			$query .= " LIMIT ".$filters['start'].",".$filters['limit'];
		}
		return $query;
	}
	
	/**
	 * Get item count
	 * 
	 * @param      integer $projectid
	 * @param      array $filters
	 * @return     integer
	 */
	public function getCount($projectid, $filters=array()) 
	{
		$filters['limit'] = 0;
		$query = "SELECT COUNT(*) ".$this->_buildQuery( $projectid, $filters );
		
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
	
	/**
	 * Delete item
	 * 
	 * @param      integer $id
	 * @param      boolean $permanent
	 * @return     boolean True on success
	 */
	public function deletePost ($id = 0, $permanent = 0 ) 
	{
		if (!$id) 
		{
			$id = $this->id;
		}
		if (!$id) 
		{
			return false;
		}
		
		$query  = ($permanent) ? "DELETE FROM $this->_tbl " : "UPDATE $this->_tbl SET state = 2 ";
		$query .= " WHERE id=".$id;
		
		$this->_db->setQuery( $query );
		if (!$this->_db->query()) 
		{
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		return true;
	}
	
	/**
	 * Delete items
	 * 
	 * @param      integer $projectid
	 * @param      boolean $permanent
	 * @return     boolean True on success
	 */
	public function deletePosts ($projectid = 0, $permanent = 0 ) 
	{
		if (!$projectid) 
		{
			$projectid = $this->projectid;
		}
		if (!$projectid) 
		{
			return false;
		}
		
		$query  = ($permanent) ? "DELETE FROM $this->_tbl " : "UPDATE $this->_tbl SET state = 2 ";
		$query .= " WHERE projectid=".$projectid;
		
		$this->_db->setQuery( $query );
		if (!$this->_db->query()) 
		{
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		return true;
	}
}
