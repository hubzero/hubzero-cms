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
 * @author    Kevin Wojkovich <kevinw@purdue.edu>
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Feeds table
 */
class FeedAggregatorTablePosts extends JTable
{
	/**
	 * int(11) Primary key
	 *
	 * @var integer
	 */
	var $id = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $title = NULL;

	/**
	 * int
	 *
	 * @var date
	 */
	var $created = NULL;

	/**
	 * varchar(255)
	 *
	 * @var varchar
	 */
	var $url = NULL;

	/**
	 * text
	 *
	 * @var text
	 */
	var $description = NULL;

	/**
	 * int(11)
	 *
	 * @var int
	 */
	var $feed_id = NULL;

	/**
	 * varchar(45)
	 *
	 * @var int
	 */
	var $status = NULL;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__feedaggregator_posts', 'id', $db);
	}

	/**
	 * Get all posts with respect to the limit of posts per page
	 *
	 * @param      integer $limit number of posts per page
	 * @param      integer $offset offset for the nth page * limit.
	 * @return     object list
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
	 * @param      integer $limit number of posts per page
	 * @param      integer $offset offset for the nth page * limit.
	 * @param      integer $status the category a post (new,apporved,under review, remove)
	 * @return     object list
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
	 * @param      integer $limit number of posts per page
	 * @param      integer $offset offset for the nth page * limit.
	 * @param      integer $status the category a post (new,apporved,under review, remove)
	 * @return     object list
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
	 * @param      integer $id id of the post to be updated
	 * @param      integer $status corresponding number assigned to status
	 * @return 	   void
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
	 * @param      integer $id ID of the feed
	 * @return     object list
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
	 * @return     array
	 */
	public function getURLs()
	{
		$query = "SELECT url FROM $this->_tbl";
		$this->_db->setQuery($query);
		return $this->_db->loadResultArray();
	}

	/**
	 *  Counts the number of posts in a specified category.
	 * @param      integer $status of category
	 * @return     integer
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

