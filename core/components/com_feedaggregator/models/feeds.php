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

require_once(dirname(__DIR__) . DS . 'tables' . DS . 'feeds.php');

/**
 * Feeds model
 */
class Feeds extends Model
{
	/**
	 * Object scope
	 *
	 * @var string
	 */
	protected $_tbl_name = '\\Components\\FeedAggregator\\Tables\\Feeds';


	/**
	 * Returns all source feeds
	 *
	 * @return  object  list of source feeds
	 */
	public function loadAll()
	{
		return $this->_tbl->getRecords();
	}

	/**
	 * Returns feed as selected by ID
	 *
	 * @param   integer  $id
	 * @return  object   list of feed
	 */
	public function loadbyId($id)
	{
		return $this->_tbl->getById($id);
	}

	/**
	 * Enables or disables a feed
	 *
	 * @param   integer  $id      ID of feed
	 * @param   integer  $status  Status of category
	 * @return  void
	 */
	public function updateActive($id, $status)
	{
		return $this->_tbl->updateActive($id, $status);
	}
}

