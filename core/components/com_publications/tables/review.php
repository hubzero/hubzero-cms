<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Publications\Tables;

/**
 * Table class for publication review
 */
class Review extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__publication_ratings', 'id', $db );
	}

	/**
	 * Validate data
	 *
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		if (trim( $this->rating ) == '')
		{
			$this->setError( Lang::txt('Your review must have a rating.') );
			return false;
		}

		if (!$this->publication_id)
		{
			$this->setError(\Lang::txt('Review entry missing Publication ID.'));
		}

		if ($this->getError())
		{
			return false;
		}

		if (!$this->created || $this->created == $this->_db->getNullDate())
		{
			$this->created = \Date::toSql();
		}

		$this->created_by = $this->created_by ?: \User::get('id');

		return true;
	}

	/**
	 * Load record
	 *
	 * @param      integer $pid       Pub ID
	 * @param      integer $uid       User ID
	 * @param      integer $versionid Pub version ID
	 * @return     mixed False if error, Object on success
	 */
	public function loadUserReview( $pid, $uid, $versionid = '' )
	{
		if ($pid === NULL)
		{
			$pid = $this->publication_id;
		}
		if ($pid === NULL )
		{
			return false;
		}

		$query  = "SELECT * FROM $this->_tbl WHERE publication_id=" . $this->_db->quote($pid)
				. " AND created_by=" . $this->_db->quote($uid);
		$query .= $versionid ? " AND publication_version_id=" . $this->_db->quote($versionid)  : '';
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
	 * Load record
	 *
	 * @param      integer $pid       Pub ID
	 * @param      integer $uid       User ID
	 * @param      integer $versionid Pub version ID
	 * @return     integer
	 */
	public function loadUserRating( $pid, $uid, $versionid = '' )
	{
		if ($pid === NULL)
		{
			$pid = $this->publication_id;
		}
		if ($pid === NULL ) {
			return false;
		}

		$query  = "SELECT rating FROM $this->_tbl WHERE publication_id=" . $this->_db->quote($pid)
				. " AND created_by=" . $this->_db->quote($uid);
		$query .= $versionid ? " AND publication_version_id=" . $this->_db->quote($versionid)  : '';
		$query .= " LIMIT 1";
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}

	/**
	 * Get records
	 *
	 * @param      integer $pid       Pub ID
	 * @param      integer $uid       User ID
	 * @param      integer $versionid Pub version ID
	 * @return     object
	 */
	public function getRatings( $pid = NULL, $uid = NULL, $versionid = '' )
	{

		if ($pid === NULL)
		{
			$pid = $this->publication_id;
		}
		if ($pid === NULL )
		{
			return false;
		}
		if (!$uid)
		{
			$uid = User::get('id');
		}

		$query = "SELECT rr.*, rr.id as id, v.helpful AS vote, "
			."\n (SELECT COUNT(*) FROM #__vote_log AS v WHERE v.helpful='yes' AND v.category='pubreview' AND v.referenceid=rr.id) AS helpful, "
			."\n (SELECT COUNT(*) FROM #__vote_log AS v WHERE v.helpful='no' AND v.category='pubreview' AND v.referenceid=rr.id) AS nothelpful "
			."\n FROM $this->_tbl AS rr "
			."\n LEFT JOIN #__vote_log AS v ON v.referenceid=rr.id AND v.category='pubreview' ";
		$query.= "AND v.voter=" . $this->_db->quote($uid);
		$query.= " WHERE rr.state IN (1, 3) AND rr.publication_id=" . $this->_db->quote($pid);
		$query.= $versionid ? " AND rr.publication_version_id=" . $this->_db->quote($versionid) : '';
		$query.= " ORDER BY rr.created DESC";
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}

	/**
	 * Get record
	 *
	 * @param      integer $pid       Pub ID
	 * @param      integer $uid       User ID
	 * @param      integer $versionid Pub version ID
	 * @return     object
	 */
	public function getRating( $pid = NULL, $uid = NULL, $versionid = '' )
	{
		if ($pid === NULL)
		{
			$pid = $this->publication_id;
		}
		if ($pid === NULL )
		{
			return false;
		}

		if (!$uid)
		{
			$uid = User::get('id');
		}

		$query = "SELECT rr.*, rr.id as id, v.helpful AS vote,
			(SELECT COUNT(*) FROM `#__vote_log` AS v WHERE v.helpful='yes' AND v.category='pubreview' AND v.referenceid=rr.id) AS helpful,
			(SELECT COUNT(*) FROM `#__vote_log` AS v WHERE v.helpful='no' AND v.category='pubreview' AND v.referenceid=rr.id) AS nothelpful
			FROM `$this->_tbl` AS rr
			LEFT JOIN `#__vote_log` AS v ON v.referenceid=rr.id AND v.category='pubreview' AND v.voter=" . $this->_db->quote($uid) . "
			WHERE rr.state IN (1, 3) AND rr.publication_id=" . $this->_db->quote($pid);
			$query.= $versionid ? " AND rr.publication_version_id=" . $this->_db->quote($versionid) : '';

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}

	/**
	 * Get vote
	 *
	 * @param      integer $id        Reference ID
	 * @param      string  $category  Category
	 * @param      integer $uid       User ID
	 * @return     mixed False if error, Object on success
	 */
	public function getVote( $id, $category = 'pubreview', $uid = NULL, $select = 'v.helpful' )
	{
		if (!$id)
		{
			$id = $this->id;
		}

		if ($id === NULL or $uid === NULL)
		{
			return false;
		}

		$query  = "SELECT $select ";
		$query .= "FROM #__vote_log as v  ";
		$query .= "WHERE v.referenceid= " . $this->_db->quote($id) . " 
					AND v.category=" . $this->_db->quote($category) . " 
					AND v.voter=" . $this->_db->quote($uid) . " LIMIT 1";
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
}
