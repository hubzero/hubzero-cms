<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Tables;

/**
 * Table class for publication author
 */
class Author extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__publication_authors', 'id', $db );
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
			$this->setError(Lang::txt('Must have an author ID.'));
			return false;
		}

		if (!$this->publication_version_id)
		{
			$this->setError(Lang::txt('Must have an item ID.'));
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
	public function loadAssociation( $uid = null, $vid = null )
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

		$query  = "SELECT * FROM $this->_tbl WHERE publication_version_id="
				. $this->_db->quote($vid) . " AND user_id=" . $this->_db->quote($uid);
		$query .= " AND (role IS NULL OR role != 'submitter') LIMIT 1";
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
	 * Load record by owner ID
	 *
	 * @param      integer $owner_id	Project owner ID
	 * @param      integer $vid 		Pub version ID
	 * @return     mixed False if error, Object on success
	 */
	public function loadAssociationByOwner( $owner_id = null, $vid = null )
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

		$query = "SELECT * FROM $this->_tbl WHERE publication_version_id="
				. $this->_db->quote($vid) . " AND project_owner_id=" . $this->_db->quote($owner_id);
		$query .= " AND (role IS NULL OR role != 'submitter') LIMIT 1";
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
	 * Get records
	 *
	 * @param      integer $vid 				Pub version ID
	 * @param      integer $get_uids 			Get user IDs
	 * @param      integer $active 				Get only active records
	 * @param      boolean $return_uid_array 	Return array
	 * @return     mixed, Object or array
	 */
	public function getAuthors( $vid = null, $get_uids = 0, $active = 1,
		$return_uid_array = false, $incSubmitter = false
	)
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
				x.picture, x.givenName, x.middleName, x.surname, x.orcid, PO.invited_name, PO.invited_email ";
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

		$query .= " WHERE A.publication_version_id=" . $this->_db->quote($vid);
		$query .= $active ? " AND A.status=1" : "";

		if ($incSubmitter == false)
		{
			$query .= " AND (A.role != 'submitter' || A.role IS NULL)";
		}

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
	 * Get publication submitter
	 *
	 * @param      integer $vid 				Pub version ID
	 * @param      integer $by					Publication creator
	 * @return     mixed False if error, Object on success
	 */
	public function getSubmitter ( $vid = null, $by = 0 )
	{
		if (!$vid)
		{
			$vid = $this->publication_version_id;
		}
		if (!$vid || !$by)
		{
			return false;
		}

		$query = "SELECT * FROM $this->_tbl WHERE role='submitter' AND publication_version_id=" . $this->_db->quote($vid) . " LIMIT 1";
		$this->_db->setQuery( $query );

		$result = $this->_db->loadObjectList();
		if ($result)
		{
			return $result[0];
		}

		$query  = "SELECT A.id as owner_id, x.uidNumber as user_id, ";
		$query .= " COALESCE( A.name , x.name ) as name, x.username, x.orcid, COALESCE( A.organization , x.organization ) as organization ";
		$query .= " FROM #__xprofiles as x  ";
		$query .= " LEFT JOIN $this->_tbl as A ON x.uidNumber=A.user_id 
		            AND A.publication_version_id=" . $this->_db->quote($vid);
		$query .= " AND A.status=1 AND A.role = 'submitter' ";
		$query .= " WHERE ( x.uidNumber=" . $this->_db->quote($by) . ")";
		$query .= " LIMIT 1 ";

		$this->_db->setQuery( $query );
		$result = $this->_db->loadObjectList();
		return $result ? $result[0] : false;
	}

	/**
	 * Save publication submitter
	 *
	 * @param      integer $vid 				Pub version ID
	 * @param      integer $by					Publication creator
	 * @return     mixed False if error, Object on success
	 */
	public function saveSubmitter ( $vid = null, $by = 0, $projectid = 0 )
	{
		if (!$vid)
		{
			$vid = $this->publication_version_id;
		}
		if (!$vid || !$by)
		{
			return false;
		}

		// Get name/org
		$author = $this->getAuthorByUid( $vid, $by );

		if (!$author)
		{
			return false;
		}

		require_once( PATH_CORE . DS . 'components' . DS
			. 'com_projects' . DS . 'tables' . DS . 'owner.php' );

		// Get project owner info
		$objO = new \Components\Projects\Tables\Owner( $this->_db );
		$owner_id = $objO->getOwnerId( $projectid, $by);

		// Load submitter record if exists
		$query = "SELECT * FROM $this->_tbl WHERE role='submitter' AND publication_version_id=" . $this->_db->quote($vid);
		$this->_db->setQuery( $query );
		if ($result = $this->_db->loadAssoc())
		{
			$this->bind( $result );
			$this->modified 	= Date::toSql();
			$this->modified_by 	= User::get('id');
		}
		else
		{
			$this->publication_version_id 	= $vid;
			$this->role 					= 'submitter';
			$this->created 					= Date::toSql();
			$this->created_by 				= User::get('id');
			$this->ordering 				= 1;
		}

		$this->project_owner_id = $owner_id;
		$this->user_id 			= $by;
		$this->status			= 1;
		$this->name				= $author->name ? $author->name : $author->p_name;
		$this->organization		= $author->organization ? $author->organization : $author->p_organization;
		$this->firstName		= $author->firstName ? $author->firstName : null;
		$this->lastName			= $author->lastName ? $author->lastName : null;
		$this->credit			= 'Submitter';
		$this->store();
	}

	/**
	 * Get record by user ID
	 *
	 * @param      integer $vid 				Pub version ID
	 * @param      integer $uid					User ID
	 * @param      integer $active 				Get only active records
	 * @return     mixed False if error, Object on success
	 */
	public function getAuthorByUid ( $vid = null, $uid = 0, $active = 0 )
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

		$query  = "SELECT A.*, x.username, x.organization as p_organization, ";
		$query .= " x.name as p_name, x.givenName, x.surname, x.username, x.orcid,
					x.picture, NULL as invited_email, NULL as invited_name, ";
		$query .= " COALESCE( A.name , x.name ) as name,
					COALESCE( A.organization , x.organization ) as organization, ";
		$query .= " COALESCE( A.firstName, x.givenName) firstName, ";
		$query .= " COALESCE( A.lastName, x.surname ) lastName ";
		$query .= " FROM #__xprofiles as x  ";
		$query .= " LEFT JOIN $this->_tbl as A ON x.uidNumber=A.user_id
		            AND A.publication_version_id=" . $this->_db->quote($vid);
		$query .= $active ? " AND A.status=1" : "";
		$query .= " WHERE x.uidNumber=" . $this->_db->quote($uid);
		$query .= " AND (A.role IS NULL OR A.role != 'submitter')  ";
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
	public function getAuthorByOwnerId ( $vid = null, $owner_id = 0 )
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

		$query  = "SELECT  A.*, po.invited_email as invited_email, po.invited_name as invited_name,  ";
		$query .= "x.name as p_name, x.username, x.organization as p_organization,
				   x.picture, x.surname, x.givenName, x.orcid, ";
		$query .= " COALESCE( A.name , x.name ) as name, x.username,
					COALESCE( A.organization , x.organization ) as organization, ";
		$query .= " COALESCE( A.firstName, x.givenName) firstName, ";
		$query .= " COALESCE( A.lastName, x.surname ) lastName ";
		$query .= " FROM #__project_owners as po  ";
		$query .= " LEFT JOIN $this->_tbl as A ON po.id=A.project_owner_id
		            AND A.publication_version_id=" . $this->_db->quote($vid);
		$query .= " LEFT JOIN #__xprofiles as x ON x.uidNumber=po.userid ";
		$query .= " AND po.status!=2 ";
		$query .= " WHERE po.id=" . $this->_db->quote($owner_id);
		$query .= " AND (A.role IS NULL OR A.role != 'submitter')  ";
		$query .= " LIMIT 1 ";

		$this->_db->setQuery( $query );
		$result = $this->_db->loadObjectList();
		return $result ? $result[0] : false;
	}

	/**
	 * Delete records
	 *
	 * @param      integer $vid    Pub version ID
	 * @return     boolean
	 */
	public function deleteAssociations( $vid = null )
	{
		if (!$vid)
		{
			return false;
		}

		$query = "DELETE FROM $this->_tbl WHERE publication_version_id=" . $this->_db->quote($vid);
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
	public function deleteAssociation( $uid = null, $vid = null, $delete = 0 )
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
			$query = "DELETE FROM $this->_tbl WHERE publication_version_id=" . $this->_db->quote($vid) . " AND user_id=" . $this->_db->quote($uid);
			$query.= " AND (role IS NULL OR role != 'submitter')  ";
		}
		else
		{
			$query = "UPDATE $this->_tbl SET status=0 WHERE publication_version_id=" . $this->_db->quote($vid) . " AND user_id=" . $this->_db->quote($uid);
			$query.= " AND (role IS NULL OR role != 'submitter')  ";
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
	public function deleteAssociationByOwner( $owner_id = null, $vid = null, $delete = 0 )
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
			$query = "DELETE FROM $this->_tbl WHERE publication_version_id=" . $this->_db->quote($vid) . " AND project_owner_id=" . $this->_db->quote($owner_id);
			$query.= " AND (role IS NULL OR role != 'submitter')  ";
		}
		else
		{
			$query = "UPDATE $this->_tbl SET status=0 WHERE publication_version_id=" . $this->_db->quote($vid) . " AND project_owner_id=" . $this->_db->quote($owner_id);
			$query.= " AND (role IS NULL OR role != 'submitter')  ";
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
		$query = "INSERT INTO $this->_tbl (	publication_version_id, user_id, ordering,
				name, firstName, lastName, organization, credit, created,
				created_by, status, project_owner_id)
				VALUES(" . $this->_db->quote($this->publication_version_id) . ", "
				. $this->_db->quote($this->user_id) . ", "
				. $this->_db->quote($this->ordering) . ", "
				. $this->_db->quote($this->name) . ", "
				. $this->_db->quote($this->firstName) . ", "
				. $this->_db->quote($this->lastName) . ", "
				. $this->_db->quote($this->organization) . ", "
				. $this->_db->quote($this->credit) . ", "
				. $this->_db->quote(Date::toSql()) . ", "
				. $this->_db->quote($this->created_by) . ", "
				. $this->_db->quote($this->status) . ", "
				. $this->_db->quote($this->project_owner_id) . ")";

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
		$query = "UPDATE $this->_tbl
					SET ordering=" . $this->_db->quote($this->ordering) . ",
					name=" . $this->_db->quote($this->name) . ",
					firstName=" . $this->_db->quote($this->firstName) . ",
					lastName=" . $this->_db->quote($this->lastName) . ",
					credit=" . $this->_db->quote($this->credit) . ",
					modified=" . $this->_db->quote($this->modified) . ",
					modified_by=" . $this->_db->quote($this->modified_by) . ",
					role=" . $this->_db->quote($this->role) . ",
					name=" . $this->_db->quote($this->name) . ",
					status=" . $this->_db->quote($this->status) . ",
					organization=" . $this->_db->quote($this->organization) . "
					WHERE publication_version_id=" . $this->_db->quote($this->publication_version_id)
					. " AND project_owner_id=" . $this->_db->quote($this->project_owner_id)
					. " AND (role IS NULL OR role != 'submitter')";

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
					x.name, x.organization, x.uidNumber, x.givenName, x.surname, x.orcid ";
		$query  .= " FROM #__project_owners as PO  ";
		$query  .= " LEFT JOIN #__xprofiles as x ON x.uidNumber=PO.userid ";
		$query  .= " WHERE PO.id=" . $this->_db->quote($owner_id) . " LIMIT 1";

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
	public function getCount( $vid = null )
	{
		if (!$vid)
		{
			$vid = $this->publication_version_id;
		}
		if (!$vid)
		{
			return false;
		}
		$this->_db->setQuery( "SELECT count(*) FROM $this->_tbl WHERE publication_version_id=" . $this->_db->quote($vid) . " AND status=1 " );
		return $this->_db->loadResult();
	}

	/**
	 * Get last order
	 *
	 * @param      integer $vid 	Pub version ID
	 * @return     integer or NULL
	 */
	public function getLastOrder( $vid = null )
	{
		if (!$vid)
		{
			$vid = $this->publication_version_id;
		}
		if (!$vid)
		{
			return false;
		}
		$this->_db->setQuery( "SELECT ordering FROM $this->_tbl
			WHERE publication_version_id=" . $this->_db->quote($vid) . "
			ORDER BY ordering DESC LIMIT 1" );
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
				$sql = "SELECT * FROM $this->_tbl WHERE publication_version_id=" . $this->_db->quote($this->publication_version_id) . " AND ordering < " . $this->_db->quote($this->ordering) . " ORDER BY ordering DESC LIMIT 1";
			break;

			case 'orderdown':
				$sql = "SELECT * FROM $this->_tbl WHERE publication_version_id=" . $this->_db->quote($this->publication_version_id) . " AND ordering > " . $this->_db->quote($this->ordering) . " ORDER BY ordering LIMIT 1";
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
