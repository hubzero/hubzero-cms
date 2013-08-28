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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Table class for publication author
 */
class PublicationAuthor extends JTable 
{
	/**
	 * int(11) Primary key
	 * 
	 * @var integer
	 */
	var $id       					= NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $publication_version_id 	= NULL;
	
	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $user_id 					= NULL;
	
	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $project_owner_id			= NULL;
	
	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $ordering					= NULL;
	
	/**
	 * varchar(50)
	 * 
	 * @var string
	 */
	var $role						= NULL;
	
	/**
	 * varchar(255)
	 * 
	 * @var string
	 */
	var $name						= NULL;
	
	/**
	 * varchar(255)
	 * 
	 * @var string
	 */
	var $firstName					= NULL;
	
	/**
	 * varchar(255)
	 * 
	 * @var string
	 */
	var $lastName					= NULL;
	
	/**
	 * varchar(255)
	 * 
	 * @var string
	 */
	var $organization				= NULL;
	
	/**
	 * varchar(255)
	 * 
	 * @var string
	 */
	var $credit						= NULL;
	
	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $status						= NULL;
	
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
	 * int(11)
	 * 
	 * @var integer
	 */
	var $modified_by				= NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 * 
	 * @var string
	 */
	var $modified					= NULL;
	
	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */	
	public function __construct( &$db ) 
	{
		parent::__construct( '#__publication_authors', 'authorid', $db );
	}
	
	/**
	 * Validate data
	 * 
	 * @return     boolean True if data is valid
	 */	
	public function check() 
	{
		if (!$this->user_id) 
		{
			$this->setError( JText::_('Must have an author ID.') );
			return false;
		}
		
		if (!$this->publication_version_id) 
		{
			$this->setError( JText::_('Must have an item ID.') );
			return false;
		}

		return true;
	}
	
	/**
	 * Load record
	 * 
	 * @param      integer $uid User ID
	 * @param      integer $vid Pub version ID
	 * @return     mixed False if error, Object on success
	 */	
	public function loadAssociation( $uid = NULL, $vid = NULL ) 
	{
		if (!$uid) 
		{
			$uid = $this->user_id;
		}
		if (!$uid) 
		{
			return false;
		}
		if (!$vid) 
		{
			$vid = $this->publication_version_id;
		}
		if (!$vid) 
		{
			return false;
		}
		
		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE publication_version_id=".$vid." AND user_id=".$uid );
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
	 * Load record by owner ID
	 * 
	 * @param      integer $owner_id	Project owner ID
	 * @param      integer $vid 		Pub version ID
	 * @return     mixed False if error, Object on success
	 */	
	public function loadAssociationByOwner( $owner_id = NULL, $vid = NULL ) 
	{
		if (!$owner_id) 
		{
			$owner_id = $this->project_owner_id;
		}
		if (!$owner_id) 
		{
			return false;
		}
		if (!$vid) 
		{
			$vid = $this->publication_version_id;
		}
		if (!$vid) 
		{
			return false;
		}
		
		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE publication_version_id=".$vid." AND project_owner_id=".$owner_id );
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
	 * Get records
	 * 
	 * @param      integer $vid 				Pub version ID
	 * @param      integer $get_uids 			Get user IDs
	 * @param      integer $active 				Get only active records
	 * @param      boolean $return_uid_array 	Return array
	 * @return     mixed, Object or array
	 */	
	public function getAuthors( $vid = NULL, $get_uids = 0, $active = 1, $return_uid_array = false ) 
	{
		if (!$vid) 
		{
			$vid = $this->publication_version_id;
		}
		if (!$vid) 
		{
			return false;
		}
		
		$query  = "SELECT ";
		$query .= $get_uids == 1
				? "A.user_id" 
				: "A.*, x.name as p_name, x.username, x.organization as p_organization, x.public as open,
				x.picture, x.givenName, x.middleName, x.surname, PO.invited_name, PO.invited_email ";
		if ($get_uids == 2) 
		{
			$query  = "SELECT A.project_owner_id";
		}
		$query .= " FROM $this->_tbl as A ";
		
		if (!$get_uids) 
		{
			$query .= " JOIN #__project_owners as PO ON PO.id=A.project_owner_id ";
			$query .= " LEFT JOIN #__xprofiles as x ON x.uidNumber=PO.userid ";
		}

		$query .= " WHERE A.publication_version_id=".$vid;
		$query .= $active ? " AND A.status=1" : "";
		$query .= " ORDER BY A.ordering ASC ";
		$this->_db->setQuery( $query );
		$results = $this->_db->loadObjectList();
		
		if ($return_uid_array) 
		{
			$uids = array();
			if ($results) 
			{				
				foreach ($results as $entry) 
				{
					if ($get_uids == 1)
					{
						if ($entry->user_id)
						{
							$uids[] = $entry->user_id;		
						}
					}
					else
					{
						$uids[] = $entry->project_owner_id;
					}
				}
			}
			return $uids;
		}
		return $results;
	}
	
	/**
	 * Get record by user ID
	 * 
	 * @param      integer $vid 				Pub version ID
	 * @param      integer $uid					User ID
	 * @param      integer $active 				Get only active records
	 * @return     mixed False if error, Object on success
	 */	
	public function getAuthorByUid ( $vid = NULL, $uid = 0, $active = 0 ) 
	{

		if (!$vid) 
		{
			$vid = $this->publication_version_id;
		}
		if (!$vid) 
		{
			return false;
		}
		if (!$uid) 
		{
			return false;
		}
				
		$query  = "SELECT ";
		$query .= " x.name as p_name, x.username, x.organization as p_organization, 
					x.picture, A.*, NULL as invited_email, NULL as invited_name ";
		$query .= " FROM #__xprofiles as x  ";
		$query .= " LEFT JOIN $this->_tbl as A ON x.uidNumber=A.user_id AND A.publication_version_id=".$vid." ";
		$query .= $active ? " AND A.status=1" : "";
		$query .= " WHERE x.uidNumber=".$uid;	
		$query .= " LIMIT 1 ";
		
		$this->_db->setQuery( $query );
		$result = $this->_db->loadObjectList();
		return $result ? $result[0] : false;
	}
	
	/**
	 * Get record by owner ID
	 * 
	 * @param      integer $vid 				Pub version ID
	 * @param      integer $owner_id			Owner ID
	 * @return     mixed False if error, Object on success
	 */	
	public function getAuthorByOwnerId ( $vid = NULL, $owner_id = 0 ) 
	{

		if (!$vid) 
		{
			$vid = $this->publication_version_id;
		}
		if (!$vid) 
		{
			return false;
		}
		if (!$owner_id) 
		{
			return false;
		}
				
		$query  = "SELECT po.invited_email as invited_email, po.invited_name as invited_name,  ";
		$query .= "x.name as p_name, x.username, x.organization as p_organization, 
				   x.picture, x.surname, x.givenName, A.* ";
		$query .= " FROM #__project_owners as po  ";
		$query .= " LEFT JOIN $this->_tbl as A ON po.id=A.project_owner_id AND A.publication_version_id=".$vid." ";
		$query .= " LEFT JOIN #__xprofiles as x ON x.uidNumber=po.userid ";
		$query .= " AND po.status!=2 ";
		$query .= " WHERE po.id=".$owner_id;	
		$query .= " LIMIT 1 ";
		
		$this->_db->setQuery( $query );
		$result = $this->_db->loadObjectList();
		return $result ? $result[0] : false;
	}
	
	/**
	 * Delete records
	 * 
	 * @param      integer $vid 				Pub version ID
	 * @return     boolean
	 */	
	public function deleteAssociations( $vid = NULL ) 
	{
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
	 * Delete record
	 * 	 
	 * @param      integer $uid					User ID
	 * @param      integer $vid 				Pub version ID
	 * @param      integer $delete 				Permanent delete?
	 * @return     boolean
	 */	
	public function deleteAssociation( $uid = NULL, $vid = NULL, $delete = 0 ) 
	{
		if (!$uid) 
		{
			$uid = $this->user_id;
		}
		if (!$uid) 
		{
			return false;
		}
		if (!$vid) 
		{
			$vid = $this->publication_version_id;
		}
		if (!$vid) 
		{
			return false;
		}
		if ($delete == 1) 
		{
			$query = "DELETE FROM $this->_tbl WHERE publication_version_id=".$vid." AND user_id=".$uid;
		}
		else 
		{
			$query = "UPDATE $this->_tbl SET status=0 WHERE publication_version_id=".$vid." AND user_id=".$uid;
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
	 * Delete record by owner ID
	 * 	 
	 * @param      integer $owner_id			Owner ID
	 * @param      integer $vid 				Pub version ID
	 * @param      integer $delete 				Permanent delete?
	 * @return     boolean
	 */	
	public function deleteAssociationByOwner( $owner_id = NULL, $vid = NULL, $delete = 0 ) 
	{
		if (!$owner_id) 
		{
			$owner_id = $this->project_owner_id;
		}
		if (!$owner_id) 
		{
			return false;
		}
		if (!$vid) 
		{
			$vid = $this->publication_version_id;
		}
		if (!$vid) 
		{
			return false;
		}
		if ($delete == 1) 
		{
			$query = "DELETE FROM $this->_tbl WHERE publication_version_id=".$vid." AND project_owner_id=".$owner_id;
		}
		else 
		{
			$query = "UPDATE $this->_tbl SET status=0 WHERE publication_version_id=".$vid." AND project_owner_id=".$owner_id;
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
	 * Create record
	 * 	 
	 * @return     boolean
	 */	
	public function createAssociation() 
	{
		$now = date( 'Y-m-d H:i:s' );
		$name = mysql_real_escape_string($this->name);
		$firstName = mysql_real_escape_string($this->firstName);
		$lastName = mysql_real_escape_string($this->lastName);
		$credit = mysql_real_escape_string($this->credit);
		$org = mysql_real_escape_string($this->organization);
		
		$query = "INSERT INTO $this->_tbl (publication_version_id, user_id, ordering, 
			role, name, firstName, lastName, organization, credit, created, 
			created_by, status, project_owner_id) VALUES ($this->publication_version_id, $this->user_id, 
			$this->ordering, '$this->role', '$name', '$firstName' , '$lastName', '$org', '$credit', 
			'$now', '$this->created_by', '$this->status', '$this->project_owner_id')";
		
		$this->_db->setQuery( $query );
		if (!$this->_db->query()) 
		{
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		return true;
	}
	
	/**
	 * Update record
	 * 	 
	 * @return     boolean
	 */	
	public function updateAssociation() 
	{
		$name = mysql_real_escape_string($this->name);
		$firstName = mysql_real_escape_string($this->firstName);
		$lastName = mysql_real_escape_string($this->lastName);
		$credit = mysql_real_escape_string($this->credit);
		$org = mysql_real_escape_string($this->organization);
		
		$query = "UPDATE $this->_tbl SET ordering=$this->ordering, role='$this->role', 
			name='$name', firstName='$firstName', lastName='$lastName', organization='$org', 
			credit='$credit', status='$this->status', modified='$this->modified', 
			modified_by='$this->modified_by' WHERE publication_version_id=$this->publication_version_id 
			AND user_id=$this->user_id";
		
		$this->_db->setQuery( $query );
		if (!$this->_db->query()) 
		{
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		return true;
	}
	
	/**
	 * Update record by owner id
	 * 	 
	 * @return     boolean
	 */	
	public function updateAssociationByOwner() 
	{
		$name = mysql_real_escape_string($this->name);
		$firstName = mysql_real_escape_string($this->firstName);
		$lastName = mysql_real_escape_string($this->lastName);
		$credit = mysql_real_escape_string($this->credit);
		$org = mysql_real_escape_string($this->organization);
		
		$query = "UPDATE $this->_tbl SET ordering=$this->ordering, role='$this->role', 
			name='$name', firstName='$firstName', lastName='$lastName', organization='$org', 
			credit='$credit', status='$this->status', modified='$this->modified', 
			modified_by='$this->modified_by' WHERE publication_version_id=$this->publication_version_id 
			AND project_owner_id=$this->project_owner_id";
		
		$this->_db->setQuery( $query );
		if (!$this->_db->query()) 
		{
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		return true;
	}
	
	/**
	 * Get profile info by owner ID
	 * 	 
	 * @param      integer $owner_id			Owner ID
	 * @return     mixed False if error, Object on success
	 */	
	public function getProfileInfoByOwner( $owner_id )
	{
		
		if (!$owner_id) {
			return false;
		}
		
		$query	 = " SELECT PO.invited_email, PO.invited_name, 
					x.name, x.organization, x.uidNumber, x.givenName, x.surname ";
		$query  .= " FROM #__project_owners as PO  ";
		$query  .= " LEFT JOIN #__xprofiles as x ON x.uidNumber=PO.userid ";
		$query  .= " WHERE PO.id=".$owner_id." LIMIT 1";
		
		$this->_db->setQuery( $query );
		$result = $this->_db->loadObjectList();
		return $result ? $result[0] : false;
		
	}
	
	/**
	 * Get record count 
	 * 
	 * @param      integer $vid 	Pub version ID
	 * @return     integer or NULL
	 */	
	public function getCount( $vid = NULL ) 
	{
		if (!$vid) 
		{
			$vid = $this->publication_version_id;
		}
		if (!$vid) 
		{
			return false;
		}
		$this->_db->setQuery( "SELECT count(*) FROM $this->_tbl WHERE publication_version_id=$vid " );
		return $this->_db->loadResult();
	}
	
	/**
	 * Get last order
	 * 
	 * @param      integer $vid 	Pub version ID
	 * @return     integer or NULL
	 */	
	public function getLastOrder( $vid = NULL ) 
	{
		if (!$vid) 
		{
			$vid = $this->publication_version_id;
		}
		if (!$vid) 
		{
			return false;
		}
		$this->_db->setQuery( "SELECT ordering FROM $this->_tbl WHERE publication_version_id=$vid ORDER BY ordering DESC LIMIT 1" );
		return $this->_db->loadResult();
	}
	
	/**
	 * Get neighbor
	 * 
	 * @param      string $move 	Direction
	 * @return     mixed False if error, Object on success    
	 */	
	public function getNeighbor( $move ) 
	{
		switch ($move) 
		{
			case 'orderup':
				$sql = "SELECT * FROM $this->_tbl WHERE publication_version_id=$this->publication_version_id 
				AND ordering < $this->ordering ORDER BY ordering DESC LIMIT 1";
				break;
			
			case 'orderdown':
				$sql = "SELECT * FROM $this->_tbl WHERE publication_version_id=$this->publication_version_id 
				AND ordering > $this->ordering ORDER BY ordering LIMIT 1";
				break;
		}
		$this->_db->setQuery( $sql );
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
