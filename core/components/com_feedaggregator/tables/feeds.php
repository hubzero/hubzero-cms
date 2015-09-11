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
class Feeds extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__feedaggregator_feeds', 'id', $db);
	}

	/**
	* Returns all source feeds
	*
	* @return  object  list of source feeds
	*/
	public function getRecords()
	{
		$query = 'SELECT * FROM '. $this->_tbl;
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Returns feed as selected by ID
	 *
	 * @param   integer  $id
	 * @return  object   list of feed
	 */
	public function getById($id = NULL)
	{
		$query = 'SELECT * FROM ' . $this->_tbl . ' WHERE id=' . (int) $id;
		$this->_db->setQuery($query);
		return $this->_db->loadObject();
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
		$query = 'UPDATE ' . $this->_tbl . ' SET enabled=' . (int) $status . ' WHERE id=' . (int) $id;
		$this->_db->setQuery($query);
		return $this->_db->query();
	}
}

