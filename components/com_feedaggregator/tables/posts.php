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
	 * varchar(255)
	 *
	 * @var string
	 *
	 *
	/**
	 * date
	 *
	 * @var date
	 */
	var $created = NULL;

	/**
	 * int(11)
	 *
	 * @var int
	 */
	var $created_by = NULL;

	var $url = NULL;

	var $description = NULL;

	var $feed_id = NULL;

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

	public function getAllPosts($limit = 10, $offset = 0)
	{
		$query = 'SELECT jos_feedaggregator_posts.id, jos_feedaggregator_posts.title, jos_feedaggregator_posts.created ,jos_feedaggregator_feeds.name, jos_feedaggregator_feeds.url, jos_feedaggregator_posts.url AS `link`, jos_feedaggregator_posts.status, jos_feedaggregator_posts.feed_id, jos_feedaggregator_posts.description FROM `jos_feedaggregator_posts` INNER JOIN `jos_feedaggregator_feeds` on jos_feedaggregator_feeds.id = jos_feedaggregator_posts.feed_id WHERE jos_feedaggregator_posts.status < 3 ORDER BY jos_feedaggregator_posts.created DESC LIMIT '.$offset.', '.$limit.';';
		$this->_db->setQuery($query);

		return $this->_db->loadObjectList();
	}
	
	public function getPostsByStatus($limit = 10, $offset = 0, $status = 0)
	{
		$query = 'SELECT jos_feedaggregator_posts.id, jos_feedaggregator_posts.title, jos_feedaggregator_posts.created ,jos_feedaggregator_feeds.name, jos_feedaggregator_feeds.url, jos_feedaggregator_posts.url AS `link`, jos_feedaggregator_posts.status, jos_feedaggregator_posts.feed_id, jos_feedaggregator_posts.description FROM `jos_feedaggregator_posts` INNER JOIN `jos_feedaggregator_feeds` on jos_feedaggregator_feeds.id = jos_feedaggregator_posts.feed_id WHERE jos_feedaggregator_posts.status = ' .$status. ' ORDER BY jos_feedaggregator_posts.created DESC LIMIT '.$offset.', '.$limit.';';
		$this->_db->setQuery($query);
		
		return $this->_db->loadObjectList();
	}

	public function getPostById($id = NULL)
	{
		$query = 'SELECT jos_feedaggregator_posts.id, jos_feedaggregator_posts.title, jos_feedaggregator_posts.created ,jos_feedaggregator_feeds.name, jos_feedaggregator_feeds.url, jos_feedaggregator_posts.url AS `link`, jos_feedaggregator_posts.status, jos_feedaggregator_posts.feed_id, jos_feedaggregator_posts.description FROM `jos_feedaggregator_posts` INNER JOIN `jos_feedaggregator_feeds` on jos_feedaggregator_feeds.id = jos_feedaggregator_posts.feed_id WHERE jos_feedaggregator_posts.id = '.$id.';';
		$this->_db->setQuery($query);

		return $this->_db->loadObjectList();
	}

	public function updateStatus($id = NULL, $status = NULL)
	{
		$query = 'UPDATE jos_feedaggregator_posts SET status= '.$status.' WHERE jos_feedaggregator_posts.id = '.$id.';';
		$this->_db->setQuery($query);
		return $this->_db->query();
	}
	
	public function getPostsbyFeedId($id = NULL)
	{
		$query = 'SELECT jos_feedaggregator_posts.id, jos_feedaggregator_posts.title, jos_feedaggregator_posts.created ,jos_feedaggregator_feeds.name, jos_feedaggregator_feeds.url, jos_feedaggregator_posts.url AS `link`, jos_feedaggregator_posts.status, jos_feedaggregator_posts.feed_id, jos_feedaggregator_posts.description FROM `jos_feedaggregator_posts` INNER JOIN `jos_feedaggregator_feeds` on jos_feedaggregator_feeds.id = jos_feedaggregator_posts.feed_id WHERE jos_feedaggregator_feeds.id= '.$id.';';

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	public function getURLs()
	{
		$query = 'SELECT url FROM jos_feedaggregator_posts';
		$this->_db->setQuery($query);
		return $this->_db->loadResultArray();
	}

	public function getRowCount()
	{
		$query = 'SELECT COUNT(*) FROM jos_feedaggregator_posts WHERE jos_feedaggregator_posts.status < 3;';
		$this->_db->setQuery($query);
		return intval($this->_db->loadResult());
	}

}

