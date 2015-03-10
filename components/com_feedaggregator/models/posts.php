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
 * @author    Kevin Wojkovich <kevinw@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Feedaggregator\Models;

use Hubzero\Base\Model;

require_once(dirname(__DIR__) . DS . 'tables' . DS . 'posts.php');

/**
 * Feed posts model
 */
class Posts extends Model
{
	/**
	 * Object scope
	 *
	 * @var  string
	 */
	protected $_tbl_name = '\\Components\\FeedAggregator\\Tables\\Posts';

	/**
	 * Get all posts with respect to the limit of posts per page
	 *
	 * @param   integer  $limit   Number of posts per page
	 * @param   integer  $offset  Offset for the nth page * limit.
	 * @return  object   list
	 */
	public function loadAllPosts($limit, $offset)
	{
		return $this->_tbl->getAllPosts($limit, $offset);
	}

	/**
	 * Get a single post by its ID
	 *
	 * @param   integer  $limit   Number of posts per page
	 * @param   integer  $offset  Offset for the nth page * limit.
	 * @param   integer  $status  The category a post (new,apporved,under review, remove)
	 * @return  object   list
	 */
	public function loadPostById($id = NULL)
	{
		return $this->_tbl->getPostById($id);
	}

	/**
	 * Update the status of a single post
	 *
	 * @param   integer  $id      ID of the post to be updated
	 * @param   integer  $status  Corresponding number assigned to status
	 * @return  void
	 */
	public function updateStatus($id = NULL, $status = NULL)
	{
		return $this->_tbl->updateStatus($id, $status);
	}

	/**
	 * Get all posts with respect to the limit of posts per page AND status category
	 *
	 * @param   integer  $limit   Number of posts per page
	 * @param   integer  $offset  Offset for the nth page * limit.
	 * @param   integer  $status  Status the category a post (new,apporved,under review, remove)
	 * @return  object   list
	 */
	public function getPostsByStatus($limit = 10, $offset = 0, $status = 0)
	{
		return $this->_tbl->getPostsByStatus($limit, $offset, $status);
	}

	/**
	 * Get posts with the specified feed id
	 *
	 * @param   integer  $id   ID of the feed
	 * @return  object   list
	 */
	public function loadPostsByFeedId($id = NULL)
	{
		return $this->_tbl->getPostsbyFeedId($id);
	}

	/**
	 * Returns an array of post URLs
	 *
	 * @return  array
	 */
	public function loadURLs()
	{
		return $this->_tbl->getURLs();
	}

	/**
	 * Counts the number of posts in a specified category.
	 *
	 * @param   integer  $status   Status of category
	 * @return  integer
	 */
	public function loadRowCount($status = NULL)
	{
		return intval($this->_tbl->getRowCount($status));
	}
}

