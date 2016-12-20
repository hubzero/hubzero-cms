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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Tables;

/**
 * Resources class for reviews
 */
class Review extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__resource_ratings', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if valid, false if not
	 */
	public function check()
	{
		if (trim($this->rating) == '')
		{
			$this->setError(\Lang::txt('Your review must have a rating.'));
		}

		if (!$this->resource_id)
		{
			$this->setError(\Lang::txt('Review entry missing Resource ID.'));
		}

		if ($this->getError())
		{
			return false;
		}

		if (!$this->created || $this->created == '0000-00-00 00:00:00')
		{
			$this->created = \Date::toSql();
		}

		$this->user_id = $this->user_id ?: \User::get('id');

		return true;
	}

	/**
	 * Load a review for a specific user/resource combination
	 *
	 * @param   integer  $resourceid  Resource ID
	 * @param   integer  $userid      User ID
	 * @return  boolean  True on success, False on error
	 */
	public function loadUserReview($resourceid, $userid)
	{
		return parent::load(array(
			'resource_id' => $resourceid,
			'user_id'     => $userid
		));
	}

	/**
	 * Load a rating for a specific user/resource combination
	 *
	 * @param   integer  $resourceid  Resource ID
	 * @param   integer  $userid      User ID
	 * @return  integer
	 */
	public function loadUserRating($resourceid, $userid)
	{
		$this->_db->setQuery("SELECT rating FROM $this->_tbl WHERE resource_id=" . $this->_db->quote($resourceid) . " AND user_id=" . $this->_db->quote($userid) . " LIMIT 1");
		return $this->_db->loadResult();
	}

	/**
	 * Get all ratings for a specific resource
	 *
	 * @param   integer  $resource_id  Resource ID
	 * @return  array
	 */
	public function getRatings($resource_id=NULL)
	{
		$resource_id = $resource_id ?: $this->resource_id;

		if (!$resource_id)
		{
			return false;
		}

		$this->_db->setQuery(
			"SELECT rr.*, rr.id as id, v.vote,
			(SELECT COUNT(*) FROM `#__item_votes` AS v WHERE v.vote='1' AND v.item_type='review' AND v.item_id=rr.id) AS helpful,
			(SELECT COUNT(*) FROM `#__item_votes` AS v WHERE v.vote='-1' AND v.item_type='review' AND v.item_id=rr.id) AS nothelpful
			FROM `$this->_tbl` AS rr
			LEFT JOIN `#__item_votes` AS v ON v.item_id=rr.id AND v.item_type='review' AND v.created_by=" . $this->_db->quote(\User::get('id')) . "
			WHERE rr.resource_id=" . $this->_db->quote($resource_id) . " AND rr.state IN (1, 3) ORDER BY rr.created DESC"
		);
		return $this->_db->loadObjectList();
	}

	/**
	 * Load rating for a specific resource
	 *
	 * @param   integer  $id      Resource ID
	 * @param   integer  $userid  User ID
	 * @return  array
	 */
	public function getRating($id=NULL, $userid)
	{
		if (!$userid)
		{
			$userid = \User::get('id');
		}

		$id = $id ?: $this->resource_id;

		if (!$id)
		{
			return false;
		}

		$this->_db->setQuery(
			"SELECT rr.*, rr.id as id, v.vote,
			(SELECT COUNT(*) FROM `#__item_votes` AS v WHERE v.vote='1' AND v.item_type='review' AND v.item_id=rr.id) AS helpful,
			(SELECT COUNT(*) FROM `#__item_votes` AS v WHERE v.vote='-1' AND v.item_type='review' AND v.item_id=rr.id) AS nothelpful
			FROM `$this->_tbl` AS rr
			LEFT JOIN `#__item_votes` AS v ON v.item_id=rr.id AND v.item_type='review' AND v.created_by=" . $this->_db->quote($userid) . "
			WHERE rr.state IN (1, 3) AND rr.id=" . $this->_db->quote($id)
		);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get the vote for a specific item and user
	 *
	 * @param   integer  $id        Resource ID
	 * @param   string   $category  Category
	 * @param   integer  $uid       User ID
	 * @return  integer
	 */
	public function getVote($id, $category = 'review', $uid)
	{
		if (!$id)
		{
			$id = $this->id;
		}

		if ($id === NULL or $uid === NULL)
		{
			return false;
		}

		$this->_db->setQuery(
			"SELECT v.vote FROM `#__item_votes` as v
			WHERE v.item_id=" . $this->_db->quote($id) . "
			AND v.item_type=" . $this->_db->quote($category) . "
			AND v.created_by=" . $this->_db->quote($uid) . " LIMIT 1"
		);
		return $this->_db->loadResult();
	}
}

