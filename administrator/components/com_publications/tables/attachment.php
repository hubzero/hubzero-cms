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
	 * ID of block element
	 *
	 * @var integer
	 */
	var $element_id 			= NULL;

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
	 * Get array of file attachments
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
	 * Get attachment used as default publication thumbnail
	 *
	 * @param      integer 	$versionid		pub version id
	 * @return     object
	 */
	public function getDefault( $versionid )
	{
		if ($versionid === NULL)
		{
			$versionid = $this->publication_version_id;
		}

		if (!$versionid)
		{
			return false;
		}

		$query  = "SELECT * FROM $this->_tbl WHERE publication_version_id=" . $versionid;
		$query .= " AND params LIKE '%pubThumb=1%' LIMIT 1";

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
	 * Save project parameter
	 *
	 * @param      object $record
	 * @param      string $param
	 * @param      string $value
	 * @return     void
	 */
	public function saveParam ( $record = NULL, $param = '', $value = 0 )
	{
		if (!is_object($record))
		{
			return false;
		}

		// Clean up value
		$value = preg_replace('/=/', '', $value);

		if ($record->params)
		{
			$params = explode("\n", $record->params);
			$in = '';
			$found = 0;

			// Change param
			if (!empty($params))
			{
				foreach ($params as $p)
				{
					if (trim($p) != '' && trim($p) != '=')
					{
						$extracted = explode('=', $p);
						if (!empty($extracted))
						{
							$in .= $extracted[0] . '=';
							$default = isset($extracted[1]) ? $extracted[1] : 0;
							$in .= $extracted[0] == $param ? $value : $default;
							$in	.= n;
							if ($extracted[0] == $param) {
								$found = 1;
							}
						}
					}
				}
			}
			if (!$found)
			{
				$in .= n . $param . '=' . $value;
			}
		}
		else
		{
			$in = $param . '=' . $value;
		}

		$record->params = $in;
		$record->store();
	}

	/**
	 * Update records for all publications in a project
	 *
	 * @param      integer 	$projectid		project id
	 * @return     void
	 */
	public function updateRecords( $projectid, $field, $from, $to )
	{
		$query = "UPDATE $this->_tbl AS A ";
		$query.= "JOIN #__publications AS B ON B.id=A.publication_id ";
		$query.= " SET A." . $field . "='$to' ";
		$query.= "WHERE A." . $field . "='$from' AND B.project_id=" . $projectid;

		$this->_db->setQuery( $query );
		if ($this->_db->query())
		{
			return true;
		}
		return false;
	}

	/**
	 * Get attachments
	 *
	 * @param      integer 	$versionid		pub version id
	 * @return     object
	 */
	public function sortAttachments( $versionid )
	{
		if ($versionid === NULL)
		{
			$versionid = $this->publication_version_id;
		}

		if (!$versionid)
		{
			return false;
		}

		$query = "SELECT a.* ";
		$query.= "FROM $this->_tbl AS a ";
		$query.= "WHERE a.publication_version_id=" . $versionid;
		$query .= " ORDER BY a.ordering ASC";

		$this->_db->setQuery( $query );
		$results = $this->_db->loadObjectList();

		if ($results)
		{
			$attachments = array(
				'1' 		=> array(),
				'2' 		=> array(),
				'3' 		=> array(),
				'elements' 	=> array()
			);

			foreach ($results as $result)
			{
				// Sort by role
				if ($result->role == 1)
				{
					$attachments['1'][] = $result;
				}
				elseif ($result->role == 2 || $result->role == 0)
				{
					$attachments['2'][] = $result;
				}
				else
				{
					$attachments['3'][] = $result;
				}

				$elementId = $result->element_id ? $result->element_id : 1;

				if (!isset($attachments['elements'][$elementId]))
				{
					$attachments['elements'][$elementId] = array();
				}

				// Add to elements array
				$attachments['elements'][$elementId][] = $result;
			}

			return $attachments;
		}

		return false;

	}

	/**
	 * Get connections
	 *
	 * @param      integer 	$vid		pub version id
	 * @param      array 	$find
	 * @return     object
	 */
	public function getConnections ( $vid = NULL, $find = array() )
	{
		if (!$vid)
		{
			$vid = $this->publication_version_id;
		}
		if (!$vid)
		{
			return false;
		}
		if (empty($find))
		{
			return false;
		}

		$query  = "SELECT * FROM $this->_tbl WHERE publication_version_id=" . $vid;
		foreach ($find as $property => $value )
		{
			$query .= " AND " . $property ."='" . $value . "'";
		}

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
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
		$element = isset($filters['element']) ? $filters['element'] : '';

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
		if ($element)
		{
			if (intval($element))
			{
				$query .= " AND a.element_id='".$element."' ";
			}
			elseif (is_array($element))
			{
				// multiple elements, TBD
			}
		}
		if (isset($filters['role']) && $filters['role'] != '')
		{
			if ($filters['role'] == 4)
			{
				$query .= " AND (a.role=0 OR a.role=2) ";
			}
			else
			{
				$query .= " AND a.role='".$filters['role']."' ";
			}
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
	 * Delete element attachment
	 *
	 * @return     object or FALSE
	 */
	public function deleteElementAttachment( $vid = NULL, $find = array(), $elementId = 0, $type = 'file', $role = '' )
	{
		if (!$vid)
		{
			$vid = $this->publication_version_id;
		}
		if (!$vid)
		{
			return false;
		}
		if (empty($find))
		{
			return false;
		}

		$query  = "DELETE FROM $this->_tbl WHERE publication_version_id=" . $vid . " AND type='" . $type . "'";
		$query .= " AND (element_id=0 OR element_id=" . intval($elementId) . ")";

		if ($role)
		{
			$query .= $role == 2 ? " AND (role = 0 OR role = 2)" : " AND role=" . intval($role);
		}

		foreach ($find as $property => $value )
		{
			$query .= " AND " . $property ."='" . $value . "'";
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
	 * Load element attachment
	 *
	 * @return     object or FALSE
	 */
	public function loadElementAttachment( $vid = NULL, $find = array(), $elementId = 0, $type = 'file', $role = '' )
	{
		if (!$vid)
		{
			$vid = $this->publication_version_id;
		}
		if (!$vid)
		{
			return false;
		}
		if (empty($find))
		{
			return false;
		}

		$query  = "SELECT * FROM $this->_tbl WHERE publication_version_id=" . $vid . " AND type='" . $type . "'";
		$query .= " AND (element_id=0 OR element_id=" . intval($elementId) . ")";

		if ($role)
		{
			$query .= $role == 2 ? " AND (role = 0 OR role = 2)" : " AND role=" . intval($role);
		}

		foreach ($find as $property => $value )
		{
			$query .= " AND " . $property ."='" . $value . "'";
		}

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
	 * Load entry by version and path/object
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

		// Import required models
		require_once( JPATH_ROOT . DS .'components' . DS . 'com_publications' . DS . 'models' . DS . 'types.php' );

		// Get types helper
		$typeHelper = new PublicationTypesHelper($this->_db);
		$prop = $typeHelper->dispatchByType($type, 'getMainProperty');
		$prop = $prop ? $prop : 'path';

		$query .= " AND " . $prop . "='" . $identifier . "'";

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

		// Import required models
		require_once( JPATH_ROOT . DS .'components' . DS . 'com_publications' . DS . 'models' . DS . 'types.php' );

		// Get types helper
		$typeHelper = new PublicationTypesHelper($this->_db);
		$prop = $typeHelper->dispatchByType($type, 'getMainProperty');
		$prop = $prop ? $prop : 'path';

		$query  = "DELETE FROM $this->_tbl WHERE publication_version_id=".$vid;
		$query .= " AND " . $prop . "='" . $identifier . "'";

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
	 * Check version duplicate
	 *
	 * @param      integer 	$pid
	 * @param      integer 	$vid
	 * @param      string 	$base
	 * @return     mixed
	 */
	public function getVersionAttachments ( $pid = NULL, $vid = NULL )
	{
		if (!intval($pid) || !intval($vid))
		{
			return false;
		}

		$query = "SELECT A.*, V.version_label, V.version_number FROM $this->_tbl AS A ";
		$query.= " JOIN #__publication_versions AS V ON A.publication_version_id = V.id ";
		$query.= " WHERE A.publication_id = ".$pid." AND A.publication_version_id !=".$vid;
		$query.= " AND V.state!='3' AND V.main=1 AND A.role=1 ";
		$query.= " ORDER BY A.ordering";
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
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