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

