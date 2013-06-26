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
 * Table class for project shared files
 */
class ProjectRemoteFile extends JTable 
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
	var $projectid       	= NULL;
		
	/**
	 * Local path to file in Git repo
	 * 
	 * @var string
	 */	
	var $local_path       	= NULL;
	
	/**
	 * Original path
	 * 
	 * @var string
	 */	
	var $original_path       = NULL;
	
	/**
	 * Original mimeType
	 * 
	 * @var string
	 */	
	var $original_format      = NULL;
		
	/**
	 * Local file format
	 * 
	 * @var string
	 */	
	var $local_format       = NULL;
	
	/**
	 * Local subdir
	 * 
	 * @var string
	 */	
	var $local_dirpath       = NULL;
	
	/**
	 * Local md5 hash
	 * 
	 * @var string
	 */	
	var $local_md5       	= NULL;
	
	/**
	 * Service name (google/dropbox)
	 * 
	 * @var string
	 */	
	var $service       		= NULL;
	
	/**
	 * Item type (file or folder)
	 * 
	 * @var string
	 */	
	var $type       		= NULL;
	
	/**
	 * Is file currently controlled by remote service?
	 * 
	 * @var integer
	 */	
	var $remote_editing    	= NULL;

	/**
	 * Remote Identifier
	 * 
	 * @var string
	 */	
	var $remote_id       	= NULL;	
	
	/**
	 * Remote Parent Id or name
	 * 
	 * @var string
	 */	
	var $remote_parent      = NULL;	
	
	/**
	 * Remote title
	 * 
	 * @var text
	 */	
	var $remote_title    	= NULL;
	
	/**
	 * Remote md5 hash
	 * 
	 * @var string
	 */	
	var $remote_md5       	= NULL;
	
	/**
	 * Remote mime type
	 * 
	 * @var string
	 */	
	var $remote_format      = NULL;
	
	/**
	 * Remote modified time (UTC)
	 * 
	 * @var string
	 */	
	var $remote_modified    = NULL;
	
	/**
	 * Remote change author
	 * 
	 * @var string
	 */	
	var $remote_author    = NULL;
	
	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $created_by			= NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 * 
	 * @var string
	 */
	var $created			= NULL;
	
	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $modified_by		= NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 * 
	 * @var string
	 */
	var $modified			= NULL;
	
	/**
	 * datetime(0000-00-00 00:00:00)
	 * 
	 * @var string
	 */
	var $synced				= NULL;
	
	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $paired				= NULL;
			
	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct( &$db ) 
	{
		parent::__construct( '#__project_remote_files', 'id', $db );
	}
	
	/**
	 * Get remote connections
	 * 
	 * @param      integer 	$projectid		Project ID
	 * @return     array
	 */
	public function getRemoteConnections ($projectid = NULL, $service = '', $local_dirpath = '', $converted = 'na')
	{
		if (!$projectid) 
		{
			return false;
		}
		
		$locals = array('paths' => array(), 'ids' => array());
		
		$query  = "SELECT local_path, local_dirpath, remote_id, synced, type, 
					remote_editing as converted, remote_parent, remote_format,
					remote_modified, paired, original_path FROM $this->_tbl"; 
		$query .= " WHERE projectid=$projectid  ";
		$query .= " AND service='" . $service . "' ";
		$query .= $local_dirpath ? " AND local_dirpath='" . $local_dirpath . "' " : '';
		if ($converted != 'na')
		{
			$converted = $converted == 1 ? 1 : 0;
			$query .= " AND remote_editing = '". $converted. "'";
		}
				
		$this->_db->setQuery( $query );
		$results = $this->_db->loadObjectList();
		
		if ($results)
		{
			foreach ($results as $result)
			{
				$item = array(
					'type' 		=> $result->type,
					'remote_id' => $result->remote_id,
					'path' 		=> $result->local_path,
					'dirpath' 	=> $result->local_dirpath,
					'format' 	=> $result->remote_format,
					'converted' => $result->converted,
					'rParent' 	=> $result->remote_parent,
					'synced' 	=> $result->synced,
					'original'	=> $result->original_path,
					'modified'	=> $result->remote_modified
				);
				
				$locals['paths'][$result->local_path] = $item;
				$locals['ids'][$result->remote_id] = $item;
			}
		}
		
		return $locals;
	}
	
	/**
	 * Get remote connections count
	 * 
	 * @param      integer 	$projectid		Project ID
	 * @return     array
	 */
	public function getFileCount ($projectid = NULL, $service = '', $converted = 'na')
	{
		if (!$projectid) 
		{
			return false;
		}
		
		$query  = "SELECT COUNT(*) FROM $this->_tbl"; 
		$query .= " WHERE projectid=$projectid  ";
		$query .= $service ? " AND service='" . $service . "' " : "";
		if ($converted != 'na')
		{
			$converted = $converted == 1 ? 1 : 0;
			$query .= " AND remote_editing = '". $converted. "'";
		}

		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
	
	/**
	 * Get remote connections
	 * 
	 * @param      integer 	$projectid		Project ID
	 * @return     array
	 */
	public function getRemoteEditFiles ($projectid = NULL, $service = '', $subdir = '')
	{
		if (!$projectid) 
		{
			return false;
		}
		
		$query  = "SELECT * FROM $this->_tbl"; 
		$query .= " WHERE projectid=$projectid  ";
		$query .= " AND service='" . $service . "' ";
		$query .= " AND remote_editing=1 ";
		$query .= " AND local_dirpath='" . $subdir . "' ";
				
		$this->_db->setQuery( $query );
		$results = $this->_db->loadObjectList();
		
		$files = array();
		if ($results)
		{
			foreach ($results as $result)
			{
				$files[$result->local_path] = $result;
			}
		}

		return $files;		
	}	
	
	/**
	 * Load item
	 * 
	 * @param      integer 	$projectid		Project ID
	 * @param      string 	$id				Remote ID
	 * @param      string	$service		Service name (google)
	 * @param      string	$local_path		Local path to file
	 * @return     mixed False if error, Object on success
	 */	
	public function loadItem ( $projectid = NULL, $id = NULL, $service = '', $local_path = '' ) 
	{
		if (!$projectid || (!$id && !$local_path)) 
		{
			return false;
		}
		
		$query  = "SELECT * FROM $this->_tbl WHERE projectid = $projectid ";
		$query .= $service ? " AND service='". $service ."' " : '';
		
		if ($id)
		{
			$query .= " AND remote_id = '". $id. "'";
		}
		else
		{
			$query .= "AND local_path = '". $local_path. "'";
		}
		
		$query .= " ORDER BY modified DESC, created DESC LIMIT 1";
		
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
	 * Get item
	 * 
	 * @param      integer 	$projectid		Project ID
	 * @param      string 	$id				Remote ID
	 * @param      string	$service		Service name (google)
	 * @param      string	$local_path		Local path to file
	 * @return     mixed False if error, Object on success
	 */
	public function getConnection( $projectid, $id, $service, $local_path, $converted = 'na' )
	{
		$query  = "SELECT id as record_id, remote_id as id, local_path as fpath,
				   	remote_parent as parent, remote_format as mimeType,
				   	remote_title as title, remote_editing as converted,
			   		service, remote_modified as modified, remote_author as author, 
					remote_md5 as md5, synced, paired, type, original_path, 
					original_format, original_id
		 		   FROM $this->_tbl WHERE projectid = $projectid ";
		$query .= $service ? " AND service='". $service ."' " : '';
		if ($id)
		{
			$query .= " AND remote_id = '". $id. "'";
		}
		else
		{
			$query .= "AND local_path = '". $local_path. "'";
		}
		if ($converted != 'na')
		{
			$converted = $converted == 1 ? 1 : 0;
			$query .= " AND remote_editing = '". $converted. "'";
		}
		$query .= " ORDER BY modified DESC, created DESC LIMIT 1";
		
		$this->_db->setQuery( $query );
		return $this->_db->loadAssoc();
	}
	
	/**
	 * Provision record for remote item
	 * 
	 * @param      integer 	$projectid		Project ID
	 * @param      string	$service		Service name (google)
	 * @param      array	$item			Remote item information array
	 * @return     mixed False if error, Object on success
	 */	
	public function checkRemoteRecord ( $projectid = NULL, $service = 'google', $uid = 0, $item = array(), $connections = array() ) 
	{		
		if (!$projectid || empty($item)) 
		{
			return false;
		}
		
		$id = $item['remoteid'];
		
		if (!$this->loadItem( $projectid, $id, $service))
		{
			$this->projectid 		= $projectid;
			$this->remote_id 		= $id;
			$this->service			= $service;
			$this->remote_parent 	= $item['rParent'];
			$this->remote_title 	= $item['title'];
			$this->remote_md5 		= $item['md5'];
			$this->type				= $item['type'];		
			$this->created			= date( 'Y-m-d H:i:s' );
			$this->created_by		= $uid;
			$this->remote_editing	= $item['converted'];
			$this->remote_format 	= $item['mimeType'];
			$this->remote_modified 	= $item['modified'];
						
			// Store local path for connection only if new
			if (!empty($connections) && !isset($connections[$item['local_path']]))
			{
				$this->local_path		= $item['local_path'];
				$this->local_dirpath	= dirname($item['local_path']) != '.' ? dirname($item['local_path']) : '';
			}
			
			if ($this->store())
			{
				return true;
			}

			return false;
		}
		
		return true;
	}
	
	/**
	 * Delete record if exists
	 * 
	 * @param      integer 	$projectid		Project ID
	 * @param      string	$service		Service name (google)
	 * @param      array	$item			Remote item information array
	 * @return     mixed False if error, Object on success
	 */	
	public function deleteRecord ( $projectid = NULL, $service = 'google', $id = 0, $filename = NULL ) 
	{
		if (!$projectid || !$id) 
		{
			return false;
		}
		
		if ($this->loadItem( $projectid, $id, $service ))
		{
			$this->delete();
		}
		
		return true;
	}
	
	/**
	 * Update record at sync
	 * 
	 * @return     mixed False if error, Object on success
	 */	
	public function updateSyncRecord ( $projectid = NULL, $service = '', $uid = 0, 
		$type = 'file', $id = NULL, $filename = NULL, $local = array(), $remote = array() ) 
	{
		if (!$projectid || (!$id && !$filename)) 
		{
			return false;
		}
		
		// Load record to update
		if (!$this->loadItem( $projectid, $id, $service, $filename))
		{
			$this->projectid 		= $projectid;
			$this->service			= $service;
			$this->created			= date( 'Y-m-d H:i:s' );
			$this->created_by		= $uid ? $uid : $this->uid;
			$this->type				= $type;			
		}
		
		$this->remote_id 		= $id ? $id : $this->remote_id;
		$this->local_path 		= $filename ? $filename : $this->local_path;
		$this->local_dirpath	= dirname($this->local_path) != '.' ? dirname($this->local_path) : '';
		$this->local_md5 		= isset($local['md5']) ? $local['md5'] : $this->local_md5;
		$this->local_format 	= isset($local['mimeType']) ? $local['mimeType'] : $this->local_format;
		
		$this->remote_parent 	= isset($remote['rParent']) ? $remote['rParent'] : $this->remote_parent;
		$this->remote_title 	= isset($remote['title']) ? $remote['title'] : $this->remote_title;
		$this->remote_md5 		= isset($remote['md5']) ? $remote['md5'] : $this->remote_md5;
		$this->remote_editing 	= isset($remote['converted']) ? $remote['converted'] : $this->remote_editing;
		$this->remote_format 	= isset($remote['mimeType']) ? $remote['mimeType'] : $this->remote_format;
		$this->remote_modified 	= isset($remote['modified']) ? $remote['modified'] : $this->remote_modified;
		$this->remote_author 	= isset($remote['author']) ? $remote['author'] : $this->remote_author;
		
		$this->modified 		= date('Y-m-d H:i:s');
		$this->modified_by		= $uid ? $uid : $this->uid;
		$this->synced 			= gmdate('Y-m-d H:i:s'); 
	
		if ($this->store())
		{
			return true;
		}

		return false;
	}
	
	/**
	 * Update record
	 * 
	 * @param      integer 	$projectid		Project ID
	 * @param      string	$service		Service name (google)
	 * @param      integer 	$id				Remote ID
	 * @return     mixed False if error, Object on success
	 */	
	public function updateRecord ( $projectid = NULL, $service = '', $id = NULL,
	 	$local_path = '', $type = 'file', $uid = 0, $parentId = 0, $title = '', 
		$remote_md5 = '', $local_md5 = '', $converted = '',
		$remote_format = '', $local_format = '', $remote_modified = '', $remote_author = '' ) 
	{
		if (!$projectid || !$id) 
		{
			return false;
		}
		
		// Load record to update
		if (!$this->loadItem( $projectid, $id, $service))
		{
			$this->projectid 		= $projectid;
			$this->remote_id 		= $id;
			$this->service			= $service;
			$this->created			= date( 'Y-m-d H:i:s' );
			$this->created_by		= $uid ? $uid : $this->uid;
		}
		
		$this->remote_parent 	= $parentId ? $parentId : $this->remote_parent;
		$this->remote_title 	= $title ? $title : $this->remote_title;
		$this->remote_md5 		= $remote_md5 ? $remote_md5 : $this->remote_md5;
		$this->local_md5 		= $local_md5 ? $local_md5 : $this->local_md5;
		
		$this->local_path 		= $local_path ? $local_path : $this->local_path;
		$this->type				= $type;		
		$this->modified 		= date('Y-m-d H:i:s');
		$this->modified_by		= $uid ? $uid : $this->uid;
		$this->synced 			= gmdate('Y-m-d H:i:s'); 
		$this->remote_editing	= $converted ? $converted : $this->remote_editing;
		$this->local_format 	= $local_format ? $local_format : $this->local_format;
		$this->remote_format 	= $remote_format ? $remote_format : $this->remote_format;
		$this->remote_modified 	= $remote_modified ? $remote_modified : $this->remote_modified;
		$this->remote_author 	= $remote_author ? $remote_author : $this->remote_author;
		$this->local_dirpath	= dirname($this->local_path) != '.' ? dirname($this->local_path) : '';
		
		if ($this->store())
		{
			return true;
		}
		
		return false;	
	}
}
