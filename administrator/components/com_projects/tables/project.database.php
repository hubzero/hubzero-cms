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
 * Table class for project databases
 */
class ProjectDatabase extends JTable 
{
	/**
	 * int(11) Primary key
	 * 
	 * @var integer
	 */
	var $id         		= NULL;

	/**
	 * Project id
	 * 
	 * @var integer
	 */	
	var $project      		= NULL;
	
	/**
	 * Database name
	 * 
	 * @var string
	 */	
	var $database_name  	= NULL;
	
	/**
	 * Database title
	 * 
	 * @var string
	 */	
	var $title  			= NULL;
	
	/**
	 * Source file
	 * 
	 * @var string
	 */	
	var $source_file  		= NULL;
	
	/**
	 * Source directory
	 * 
	 * @var string
	 */	
	var $source_dir  		= NULL;
	
	/**
	 * Source revision
	 * 
	 * @var string
	 */	
	var $source_revision  	= NULL;
	
	/**
	 * Description
	 * 
	 * @var text
	 */	
	var $description 		= NULL;
	
	/**
	 * Data definition
	 * 
	 * @var text
	 */	
	var $data_definition  	= NULL;
	
	/**
	 * Revision
	 * 
	 * @var integer
	 */	
	var $revision      		= NULL;	
			
	/**
	 * Created (0000-00-00 00:00:00)
	 * 
	 * @var datetime
	 */
	var $created			= NULL;
	
	/**
	 * Created by
	 * 
	 * @var integer
	 */	
	var $created_by      	= NULL;	
	
	/**
	 * Updated (0000-00-00 00:00:00)
	 * 
	 * @var datetime
	 */
	var $updated			= NULL;
	
	/**
	 * Updated by
	 * 
	 * @var integer
	 */	
	var $updated_by      	= NULL;	
		
	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct( &$db ) 
	{
		parent::__construct( '#__project_databases', 'id', $db );
	}
	
	/**
	 * Load a record and bind to $this
	 * 
	 * @param      string 	$identifier 	database name or id
	 * @return     object or false
	 */
	public function loadRecord ( $identifier = NULL ) 
	{
		if ($identifier === NULL) 
		{
			return false;
		}
		$name = is_numeric($identifier) ? 'id' : 'database_name';
		
		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE $name='$identifier' LIMIT 1" );
		if ($result = $this->_db->loadAssoc()) 
		{
			return $this->bind( $result );
		} 
		else 
		{
			return false;
		}
	}
	
	/**
	 * Get items
	 * 
	 * @param      integer 		$projectid
	 * @param      array 		$filters
	 * @param      boolean 		$skipPublished
	 * @return     object, integer or NULL
	 */
	public function getItems( $projectid = NULL, $filters = array(), $skipPublished = false)
	{		
		if ($projectid == NULL) 
		{
		 	return false;
		}
		
		$count  		= isset($filters['count']) ? $filters['count'] : 0;
		
		$query  = "SELECT ";
		$query .= $count ? " COUNT(*) " : "*";
		$query .= " FROM $this->_tbl ";
		$query .= " WHERE project = '".$projectid."' ";
		$query .= $skipPublished ? " AND revision IS NULL" : "";

		$this->_db->setQuery( $query );
		return $count ? $this->_db->loadResult() :  $this->_db->loadObjectList();
	}
	
	/**
	 * Get items
	 * 
	 * @param      integer $projectid
	 * @param      array $filters
	 * @return     object, integer or NULL
	 */
	public function getResource( $projectid = NULL, $id = NULL)
	{		
		if ($projectid == NULL || $id == NULL) 
		{
		 	return false;
		}
		
		$query = "SELECT database_name, title FROM $this->_tbl WHERE id=$id AND project=" . $projectid;

		$this->_db->setQuery( $query );
		return $this->_db->loadAssoc();
	}	
	
	/**
	 * Get database list
	 * 
	 * @param      integer $projectid
	 * @return     object, integer or NULL
	 */
	public function getList( $projectid = NULL)
	{		
		if ($projectid == NULL) 
		{
		 	return false;
		}
		
		$query = "SELECT db.id, db.project, db.database_name, db.title, db.source_file, 
				db.source_dir, db.source_revision, db.description, db.data_definition, 
				db.revision, db.created, db.created_by, c.name 
				FROM $this->_tbl AS db LEFT JOIN #__users AS c ON (c.id=db.created_by) 
				WHERE project = " . $projectid . " ORDER BY db.created DESC";

		$this->_db->setQuery( $query );
		return $this->_db->loadAssocList();
	}
	
	/**
	 * Get used items
	 * 
	 * @param      integer $projectid
	 * @return     object, integer or NULL
	 */
	public function getUsedItems( $projectid = NULL)
	{		
		if ($projectid == NULL) 
		{
		 	return false;
		}
		
		$query = "SELECT DISTINCT IF(source_dir != '', CONCAT(source_dir, '/', source_file), source_file) as file 
				  FROM $this->_tbl
				  WHERE project = " . $projectid;		

		$this->_db->setQuery( $query );
		return $this->_db->loadResultArray();
	}
}
