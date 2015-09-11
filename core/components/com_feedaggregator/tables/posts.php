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
 * @author    Kevin Wojkovich <kevinw@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Feedaggregator\Tables;

/**
 * Feeds table
 */
class Posts extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__feedaggregator_posts', 'id', $db);
	}

	/**
	 * Get all posts with respect to the limit of posts per page
	 *
	 * @param   integer  $limit   number of posts per page
	 * @param   integer  $offset  offset for the nth page * limit.
	 * @return  object   list
	 */
	public function getAllPosts($limit = 10, $offset = 0)
	{
		$query = "SELECT p.id, p.title, p.created, f.name, f.url, p.url AS `link`, p.status, p.feed_id, p.description
				 FROM `$this->_tbl` AS p
				 INNER JOIN `#__feedaggregator_feeds` AS f ON f.id=p.feed_id
				 WHERE p.status < 3 ORDER BY p.created DESC LIMIT " . (int) $offset . ", " . (int) $limit;
		$this->_db->setQuery($query);

		return $this->_db->loadObjectList();
	}

	/**
	 * Get all posts with respect to the limit of posts per page AND status category
	 *
	 * @param   integer  $limit   number of posts per page
	 * @param   integer  $offset  offset for the nth page * limit.
	 * @param   integer  $status  the category a post (new,apporved,under review, remove)
	 * @return  object   list
	 */
	public function getPostsByStatus($limit = 10, $offset = 0, $status = 0)
	{
		$query = "SELECT p.id, p.title, p.created, f.name, f.url, p.url AS `link`, p.status, p.feed_id, p.description
				 FROM `$this->_tbl` AS p
				 INNER JOIN `#__feedaggregator_feeds` AS f ON f.id=p.feed_id
				 WHERE p.status = " . (int) $status . "
				 ORDER BY p.created DESC LIMIT " . (int) $offset . ", " . (int) $limit;
		$this->_db->setQuery($query);

		return $this->_db->loadObjectList();
	}

	/**
	 * Get a single post by its ID
	 *
	 * @param   integer  $limit   number of posts per page
	 * @param   integer  $offset  offset for the nth page * limit.
	 * @param   integer  $status  the category a post (new,apporved,under review, remove)
	 * @return  object   list
	 */
	public function getPostById($id = NULL)
	{
		$query = "SELECT p.id, p.title, p.created, f.name, f.url, p.url AS `link`, p.status, p.feed_id, p.description
				 FROM `$this->_tbl` AS p
				 INNER JOIN `#__feedaggregator_feeds` AS f ON f.id=p.feed_id
				 WHERE p.id = " . (int) $id;
		$this->_db->setQuery($query);

		return $this->_db->loadObjectList();
	}

	/**
	 * Update the status of a single post
	 *
	 * @param   integer  $id      id of the post to be updated
	 * @param   integer  $status  corresponding number assigned to status
	 * @return  void
	 */
	public function updateStatus($id = NULL, $status = NULL)
	{
		$query = "UPDATE $this->_tbl SET status=" . (int) $status . " WHERE id=" . $id;
		$this->_db->setQuery($query);
		return $this->_db->query();
	}

	/**
	 * Get posts with the specified feed id
	 *
	 * @param   integer  $id  ID of the feed
	 * @return  object   list
	 */
	public function getPostsbyFeedId($id = NULL)
	{
		$query = "SELECT p.id, p.title, p.created, f.name, f.url, p.url AS `link`, p.status, p.feed_id, p.description
				 FROM `{$this->_tbl}` AS p
				 INNER JOIN `#__feedaggregator_feeds` AS f ON f.id=p.feed_id
				 WHERE f.id=" . (int) $id;

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Returns an array of post URLs
	 *
	 * @return  array
	 */
	public function getURLs()
	{
		$query = "SELECT url FROM $this->_tbl";
		$this->_db->setQuery($query);
		return $this->_db->loadColumn();
	}

	/**
	 * Counts the number of posts in a specified category.
	 *
	 * @param   integer  $status  Status of category
	 * @return  integer
	 */
	public function getRowCount($status = NULL)
	{
		$query = "SELECT COUNT(*) FROM $this->_tbl WHERE status";
		if ($status !== null)
		{
			$query .= '=' . (int) $status;
		}
		else
		{
			$query .= '< 3';
		}

		$this->_db->setQuery($query);
		return intval($this->_db->loadResult());
	}
}

