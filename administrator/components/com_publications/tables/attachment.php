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
 * Table class for publication attachments
 */
class PublicationAttachment extends JTable 
{
	/**
	 * int(11) Primary key
	 * 
	 * @var integer
	 */
	var $id         			= NULL;
	
	/**
	 * Publication version ID
	 * 
	 * @var integer
	 */
	var $publication_version_id = NULL;
	
	/**
	 * Publication  ID
	 * 
	 * @var integer
	 */
	var $publication_id 		= NULL;
	
	/**
	 * Attached object ID
	 * 
	 * @var integer
	 */
	var $object_id 				= NULL;
	
	/**
	 * Attached object name
	 * 
	 * @var integer
	 */
	var $object_name 			= NULL;
	
	/**
	 * Attached object instance ID
	 * 
	 * @var integer
	 */
	var $object_instance 		= NULL;
	
	/**
	 * Attached object revision number
	 * 
	 * @var integer
	 */
	var $object_revision 		= NULL;
	
	/**
	 * Title
	 * 
	 * @var string
	 */	
	var $title       			= NULL;
	
	/**
	 * Path
	 * 
	 * @var string
	 */	
	var $path       			= NULL;
	
	/**
	 * Type
	 * 
	 * @var string
	 */	
	var $type       			= NULL;

	/**
	 * Created by user ID
	 * 
	 * @var integer
	 */
	var $created_by        		= NULL;
	
	/**
	 * Created, datetime (0000-00-00 00:00:00)
	 * 
	 * @var datetime
	 */
	var $created				= NULL;
	
	/**
	 * Modified by user ID
	 * 
	 * @var integer
	 */
	var $modified_by        	= NULL;
	
	/**
	 * Modified, datetime (0000-00-00 00:00:00)
	 * 
	 * @var datetime
	 */
	var $modified				= NULL;
	
	/**
	 * VCS revision
	 * 
	 * @var string
	 */	
	var $vcs_revision       	= NULL;
	
	/**
	 * VCS hash
	 * 
	 * @var string
	 */	
	var $vcs_hash       		= NULL;
	
	/**
	 * Content hash
	 * 
	 * @var string
	 */	
	var $content_hash       	= NULL;
	
	/**
	 * Ordering
	 * 
	 * @var integer
	 */	
	var $ordering       		= NULL;

	/**
	 * Role (primary - 1, secondary - 0)
	 * 
	 * @var integer
	 */	
	var $role       			= NULL;
	
	/**
	 * Params
	 * 
	 * @var text
	 */	
	var $params       			= NULL;
	
	/**
	 * Attributes
	 * 
	 * @var text
	 */	
	var $attribs       			= NULL;
	
	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */	
	public function __construct( &$db )
	{
		parent::__construct( '#__publication_attachments', 'id', $db );
	}
	
	/**
	 * Get array of attachments
	 * 
	 * @param      integer 	$versionid 	Pub version ID
	 * @return     object
	 */	
	public function getAttachmentsArray( $versionid = null, $role = '')
	{
		if ($versionid === NULL) 
		{
			return false;
		}
		
		$result = array();
		
		$filters = array('role' => $role);
		$items = $this->getAttachments($versionid, $filters);
		
		if ($items)
		{
			foreach ($items as $item)
			{
				$result[] = $item->path;
			}
		}
		
		return $result;
	}
		
	/**
	 * Get attachments
	 * 
	 * @param      integer 	$versionid		pub version id
	 * @param      array 	$filters
	 * @return     object
	 */	
	public function getAttachments( $versionid, $filters=array() ) 
	{
		if ($versionid === NULL) 
		{
			$versionid = $this->publication_version_id;
		}

		$project = isset($filters['project']) && $filters['project'] != '' ? intval($filters['project']) : '';
		$count 	 = isset($filters['count']) && $filters['count'] == 1 ? 1 : 0;
		$select  = isset($filters['select']) && $filters['select'] != '' ? $filters['select'] : '';
		$aid  	 = isset($filters['id']) && intval($filters['id']) != 0 ? intval($filters['id']) : '';
		$type  	 = isset($filters['type']) && $filters['type'] != '' ? $filters['type'] : '';
	
		if ($versionid === NULL && !$project) 
		{
			return false;
		}		
	
		$query = $count ? "SELECT COUNT(*) " : "SELECT a.* ";
		if ($project && !$count) 
		{
			$query.= ", p.id as publication_id, p.project_id  ";
		}
		if ($select) 
		{
			$query = "SELECT ".$select." ";
		}
		elseif ($type == 'publication')
		{
			$query.= ", p.id as publication_id, p.project_id  ";
		}
		
		$query.= "FROM $this->_tbl AS a ";
		if ($project || $type) 
		{
			$query .= "JOIN #__publication_versions AS v ON v.id=a.publication_version_id ";
			$query .= "JOIN #__publications AS p ON p.id=v.publication_id ";
		}
		$query.= "WHERE ";
		if ($aid) 
		{
			$query .= " a.id='".$aid."' LIMIT 1 ";
			$this->_db->setQuery( $query );
			return $this->_db->loadObjectList();
		}
		else 
		{
			$query.= intval($project) ? " p.project_id=".$project : " a.publication_version_id=".$versionid;	
		}
	
		if (isset($filters['role']) && $filters['role'] != '') 
		{
			$role 	= $filters['role'] == 4 ? 0 : $filters['role'];
			$query .= " AND a.role='".$role."' ";
		}
		if ($type) 
		{
			$query .= " AND a.type='".$type."' ";
		}			
		if (isset($filters['order']) && $filters['order'] != '') 
		{
			$query .= " ORDER BY ".$filters['order'];
		} 
		else 
		{
			$query .= " ORDER BY a.ordering ASC";
		}
		if (isset($filters['limit']) && $filters['limit'] != 0 && !$count) 
		{
			$query .= " LIMIT ".$filters['start'].",".$filters['limit'];
		}

		$this->_db->setQuery( $query );
		return $count ? $this->_db->loadResult() : $this->_db->loadObjectList();

	}
	
	/**
	 * Load entry by version and path
	 * 
	 * @param      integer 	$vid		pub version id
	 * @param      string 	$identifier	Attached object identifier (e.g. content path for files)
	 * @param      string 	$type		Attachment type
	 * @return     object or FALSE
	 */	
	public function loadAttachment( $vid = NULL, $identifier = NULL, $type = 'file' ) 
	{
		if (!$vid) 
		{
			$vid = $this->publication_version_id;
		}
		if (!$vid) 
		{
			return false;
		}
		if (!$identifier) 
		{
			return false;
		}
		
		$query  = "SELECT * FROM $this->_tbl WHERE publication_version_id=" . $vid . " AND type='" . $type . "'";
		
		if ($type == 'file')
		{
			$query .= " AND path='" . $identifier . "'";
		}
		
		if ($type == 'data' || $type == 'app')
		{
			$query .= is_numeric($identifier) ? " AND object_id='" . $identifier . "'" : " AND object_name='" . $identifier . "'";
		}		
		
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
	 * Load entry by version and path
	 * 
	 * @param      integer 	$vid		pub version id
	 * @param      string 	$identifier	content path or object id/name
	 * @return     boolean
	 */	
	public function deleteAttachment( $vid = NULL, $identifier = NULL, $type = 'file' ) 
	{
		if (!$vid) 
		{
			$vid = $this->publication_version_id;
		}
		if (!$vid) 
		{
			return false;
		}
		if (!$identifier) 
		{
			return false;
		}
		
		$query = "DELETE FROM $this->_tbl WHERE publication_version_id=".$vid;
		if ($type == 'file')
		{
			$query .= " AND path='" . $identifier . "'";
		}
		
		if ($type == 'data' || $type == 'app')
		{
			$query .= is_numeric($identifier) ? " AND object_id='" . $identifier . "'" : " AND object_name='" . $identifier . "'";
		}		
		$this->_db->setQuery( $query );
		if (!$this->_db->query()) 
		{
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		return true;
	}	
	
	/**
	 * Load entries by pub version
	 * 
	 * @param      integer 	$vid		pub version id
	 * @return     boolean
	 */	
	public function deleteAttachments( $vid = NULL ) 
	{
		if (!$vid) 
		{
			$vid = $this->publication_version_id;
		}
		if (!$vid) 
		{
			return false;
		}
		
		$query = "DELETE FROM $this->_tbl WHERE publication_version_id=".$vid;		
		$this->_db->setQuery( $query );
		if (!$this->_db->query()) 
		{
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		return true;
	}
	
	/**
	 * Check used
	 * 
	 * @param      string 	$base
	 * @param      array 	$selections
	 * @param      integer 	$projectid
	 * @param      integer 	$pid
	 * @return     object or FALSE
	 */	
	public function checkUsed ( $base = 'files', $selections = array(), $projectid = NULL, $pid = NULL ) 
	{
		if (!$projectid || empty($selections)) 
		{
			return false;
		}
		
		$query = "SELECT DISTINCT P.id, V.title, A.path FROM #__publications AS P ";
		$query.= " JOIN $this->_tbl AS A ON A.publication_id = P.id ";
		$query.= " JOIN #__publication_versions AS V ON A.publication_id = V.publication_id AND V.main=1 ";
		$query.= " WHERE P.id != ".$pid;

		if ($base == 'files' && !empty($selections['files']))
		{
			$files = '';
			foreach ($selections['files'] as $file) 
			{
				$files .= '"'.$file.'",';	
			}
			$files = substr($files,0,strlen($files) - 1);
				
			$query.= " AND A.path IN(" . $files . ")  ";		
		}
		
		elseif ($base == 'databases' && !empty($selections['data']))
		{
			$ids = '';
			foreach ($selections['data'] as $data) 
			{
				$ids .= '"'.$data.'",';	
			}
			$ids = substr($ids, 0, strlen($ids) - 1);

			$query.= " AND A.object_name IN(" . $ids . ")  ";		
		}
		
		elseif ($base == 'notes' && !empty($selections['notes']))
		{
			$ids = '';
			foreach ($selections['notes'] as $note) 
			{
				$ids .= '"'.$note.'",';	
			}
			$ids = substr($ids, 0, strlen($ids) - 1);

			$query.= " AND A.object_id IN(" . $ids . ")  ";		
		}
		
		$query.= " AND A.role=1 AND P.project_id=" . $projectid;
		$query.= " GROUP BY P.id";
		
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
	
	/**
	 * Check version duplicate
	 * 
	 * @param      integer 	$count
	 * @param      array 	$info
	 * @param      integer 	$pid
	 * @param      integer 	$vid
	 * @param      string 	$base
	 * @return     mixed
	 */	
	public function checkVersionDuplicate ( $count = 0, $info = array(), $pid = NULL, $vid = NULL, $base = 'files' ) 
	{		
		if ($count == 0 || empty($info) || !$pid || !$vid) 
		{
			return false;
		}
		
		$query = "SELECT A.*, V.version_label, V.version_number FROM $this->_tbl AS A ";
		$query.= " JOIN #__publication_versions AS V ON A.publication_version_id = V.id ";
		$query.= " WHERE A.publication_id = ".$pid." AND A.publication_version_id !=".$vid;
		$query.= " AND V.state!='3' AND V.main=1 AND A.role=1 ";
		$query.= " ORDER BY A.ordering";
		$this->_db->setQuery( $query );
		$result = $this->_db->loadObjectList();
			
		if (!$result) 
		{
			return false;
		}
		else 
		{
			$matched   = 0;
			foreach ($info as $o)
			{
				foreach ($result as $r)
				{
					if ($base == 'files' && $o['path'] == $r->path && $o['hash'] == $r->vcs_hash)
					{
						$matched++;
					}
					elseif ($base == 'databases' && $o['object_name'] == $r->object_name 
						&& $o['object_revision'] == $r->object_revision && $r->type == 'data')
					{
						$matched++;
					}
					elseif ($base == 'notes'  && $o['object_id'] == $r->object_id 
						&& $o['object_revision'] == $r->object_revision && $r->type == 'note')
					{
						$matched++;
					}
				}
			}
			
			if ($matched == $count)
			{
				return $result[0]->version_label;
			}
		}
		
		return false;
	}
	
	/**
	 * Get pub association
	 * 
	 * @param      integer 	$projectid
	 * @param      string	$path
	 * @param      string 	$hash
	 * @param      integer 	$primary
	 * @return     array
	 */	
	public function getPubAssociation ( $projectid = 0, $path = '', $hash = '', $primary = 1 ) 
	{	
		if (!$projectid || (!$hash && !$path)) 
		{
			return false;
		}
		$pub   = array('id'=> '', 'title' => '', 'version' => 'default', 'version_label' => '');
		$query = "SELECT a.publication_id , v.title, v.version_number, v.version_label FROM $this->_tbl as a ";
		$query.= "JOIN #__publication_versions AS v ON v.id=a.publication_version_id  ";
		$query.= "JOIN #__publications AS P ON P.id=v.publication_id  ";
		$query.= " WHERE P.project_id=".$projectid." ";
		$query.= $hash ? "AND a.vcs_hash='".$hash."' " : "AND a.path='".$path."' ";
		$query.= $primary ? " AND a.role=1 " : "";
		$query.= "ORDER BY v.id DESC LIMIT 1";
		$this->_db->setQuery( $query );
		$result = $this->_db->loadObjectList();
		
		if ($result) 
		{
			$pub['id'] = $result[0]->publication_id;
			$pub['title'] = $result[0]->title;
			$pub['version'] = $result[0]->version_number;
			$pub['version_label'] = $result[0]->version_label;
		}
		
		return $pub;
	}	
	
	/**
	 * Get pub associations
	 * 
	 * @param      integer 	$projectid
	 * @param      string	$type
	 * @param      integer 	$primary
	 * @return     array
	 */	
	public function getPubAssociations ( $projectid = 0, $type = 'file', $primary = 1) 
	{	
		if (!$projectid ) 
		{
			return false;
		}
		
		$assoc = array();
		
		$query = "SELECT a.path, a.publication_id , v.title, v.version_number, v.version_label FROM $this->_tbl as a ";
		$query.= "JOIN #__publication_versions AS v ON v.id=a.publication_version_id  ";
		$query.= "JOIN #__publications AS P ON P.id=v.publication_id  ";
		$query.= " WHERE P.project_id=".$projectid." AND a.type='$type' ";
		$query.= $primary ? " AND a.role=1 " : "";
		$query.= " GROUP BY a.path ";
		$query.= " ORDER BY v.id DESC ";
		$this->_db->setQuery( $query );
		$result = $this->_db->loadObjectList();
		
		if ($result) 
		{
			foreach ($result as $r)
			{
				$pub = array();
				$pub['id'] = $r->publication_id;
				$pub['title'] = $r->title;
				$pub['version'] = $r->version_number;
				$pub['version_label'] = $r->version_label;
								
				$assoc[$r->path][] = $pub; 
			}
		}
		
		return $assoc;
	}
}