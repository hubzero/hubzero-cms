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
 * Table class for resource media tracking
 */
class MediaTracking extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__media_tracking', 'id', $db);
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
	 * Get tracking info for a specific user/resource combination
	 *
	 * @param   string  $user_id      User ID
	 * @param   string  $object_id    Object ID
	 * @param   string  $object_type  Object type
	 * @return  object
	 */
	public function getTrackingInformationForUserAndResource($user_id = '', $object_id = '', $object_type = 'resource')
	{
		// Make sure we have a resource
		if (!$object_id)
		{
			return;
		}

		$sql = "SELECT m.* FROM $this->_tbl AS m WHERE ";

		// If we don't have a user ID use session ID
		if (!$user_id)
		{
			$session = \App::get('session');
			$session_id = $session->getId();
			$sql .= "m.session_id=" . $this->_db->quote($session_id);
		}
		else
		{
			$sql .= "m.user_id=" . $this->_db->quote($user_id);
		}

		$sql .= " AND m.object_id=" . $this->_db->quote($object_id) . " AND m.object_type=" . $this->_db->quote($object_type);

		$this->_db->setQuery($sql);
		return $this->_db->loadObject();
	}
}