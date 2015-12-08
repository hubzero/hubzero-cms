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

namespace Components\System\Tables;

/**
 * Table class for resource detailed media tracking
 */
class MediaTrackingDetailed extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__media_tracking_detailed', 'id', $db);
	}

	/**
	 * Check method used to verify data on save
	 * 
	 * @return  bool  Validation check result
	 */
	public function check()
	{
		// session id check
		if (trim($this->session_id) == '')
		{
			$this->setError(\Lang::txt('Missing required session identifier.'));
		}

		// IP check
		if (trim($this->ip_address) == '')
		{
			$this->setError(\Lang::txt('Missing required session identifier.'));
		}

		// object id/type check
		if (trim($this->object_id) == '' || trim($this->object_type) == '')
		{
			$this->setError(\Lang::txt('Missing required object id or object type.'));
		}

		if ($this->getError())
		{
			return false;
		}

		return true;
	}

	/**
	 * Load a record by ID
	 *
	 * @param   integer  $id  Record ID
	 * @return  object
	 */
	public function loadByDetailId($id)
	{
		$this->_db->setQuery("SELECT m.* FROM $this->_tbl AS m WHERE id=" . $this->_db->quote($id));
		return $this->_db->loadObject();
	}
}