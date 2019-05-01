<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Tables;

use Hubzero\Database\Table;
use Lang;
use User;
use Date;

/**
 * Table class for publication review
 */
class Review extends Table
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__publication_ratings', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		if (trim($this->rating) == '')
		{
			$this->setError(Lang::txt('Your review must have a rating.'));
			return false;
		}

		if (!$this->publication_id)
		{
			$this->setError(Lang::txt('Review entry missing Publication ID.'));
		}

		if ($this->getError())
		{
			return false;
		}

		if (!$this->created || $this->created == $this->_db->getNullDate())
		{
			$this->created = Date::toSql();
		}

		$this->created_by = $this->created_by ?: User::get('id');

		return true;
	}

	/**
	 * Load record
	 *
	 * @param   integer  $pid        Pub ID
	 * @param   integer  $uid        User ID
	 * @param   integer  $versionid  Pub version ID
	 * @return  mixed    False if error, Object on success
	 */
	public function loadUserReview($pid, $uid, $versionid = '')
	{
		if ($pid === null)
		{
			$pid = $this->publication_id;
		}
		if ($pid === null)
		{
			return false;
		}

		$query  = "SELECT * FROM `$this->_tbl` WHERE publication_id=" . $this->_db->quote($pid) . " AND created_by=" . $this->_db->quote($uid);
		$query .= $versionid ? " AND publication_version_id=" . $this->_db->quote($versionid) : '';
		$query .= " LIMIT 1";
		$this->_db->setQuery($query);

		if ($result = $this->_db->loadAssoc())
		{
			return $this->bind($result);
		}
		else
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
	}

	/**
	 * Load record
	 *
	 * @param   integer  $pid        Pub ID
	 * @param   integer  $uid        User ID
	 * @param   integer  $versionid  Pub version ID
	 * @return  mixed
	 */
	public function loadUserRating($pid, $uid, $versionid = '')
	{
		if ($pid === null)
		{
			$pid = $this->publication_id;
		}
		if ($pid === null)
		{
			return false;
		}

		$query  = "SELECT rating FROM `$this->_tbl` WHERE publication_id=" . $this->_db->quote($pid) . " AND created_by=" . $this->_db->quote($uid);
		$query .= $versionid ? " AND publication_version_id=" . $this->_db->quote($versionid) : '';
		$query .= " LIMIT 1";
		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get records
	 *
	 * @param   integer  $pid        Pub ID
	 * @param   integer  $uid        User ID
	 * @param   integer  $versionid  Pub version ID
	 * @return  object
	 */
	public function getRatings($pid = null, $uid = null, $versionid = '')
	{
		if ($pid === null)
		{
			$pid = $this->publication_id;
		}
		if ($pid === null)
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
		$query.= " ORDER BY rr.created DESC";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get records
	 *
	 * @param   integer  $pid        Pub ID
	 * @param   integer  $uid        User ID
	 * @param   integer  $versionid  Pub version ID
	 * @return  object
	 */
	public function countRatings($pid = null, $uid = null, $versionid = '')
	{
		if ($pid === null)
		{
			$pid = $this->publication_id;
		}
		if ($pid === null)
		{
			return false;
		}
		if (!$uid)
		{
			$uid = User::get('id');
		}

		$query = "SELECT COUNT(rr.id)
				FROM `$this->_tbl` AS rr
				WHERE rr.state IN (1, 3)
				AND rr.publication_id=" . $this->_db->quote($pid);
		$query .= $versionid ? " AND rr.publication_version_id=" . $this->_db->quote($versionid) : '';
		$query .= " ORDER BY rr.created DESC";

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get record
	 *
	 * @param   integer  $pid        Pub ID
	 * @param   integer  $uid        User ID
	 * @param   integer  $versionid  Pub version ID
	 * @return  object
	 */
	public function getRating($pid = null, $uid = null, $versionid = '')
	{
		if ($pid === null)
		{
			$pid = $this->publication_id;
		}
		if ($pid === null)
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

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get vote
	 *
	 * @param   integer  $id        Reference ID
	 * @param   string   $category  Category
	 * @param   integer  $uid       User ID
	 * @return  mixed    False if error, Object on success
	 */
	public function getVote($id, $category = 'pubreview', $uid = null, $select = 'v.helpful')
	{
		if (!$id)
		{
			$id = $this->id;
		}

		if ($id === null or $uid === null)
		{
			return false;
		}

		$query = "SELECT $select
			FROM `#__vote_log` as v
			WHERE v.referenceid= " . $this->_db->quote($id) . "
			AND v.category=" . $this->_db->quote($category) . "
			AND v.voter=" . $this->_db->quote($uid) . "
			LIMIT 1";

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}
}
