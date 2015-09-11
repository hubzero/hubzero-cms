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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Answers\Tables;

use Hubzero\Utility\Validate;
use Lang;

/**
 * Table class for answer votes
 */
class Log extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__answers_log', 'id', $db);
	}

	/**
	 * Load a record and bind to $this
	 *
	 * @param   integer  $response_id  Answer ID
	 * @param   string   $ip           IP address
	 * @return  boolean  True upon success, False if errors
	 */
	public function loadByIp($response_id=null, $ip=null)
	{
		$response_id = $response_id ?: $this->response_id;

		if ($response_id == null)
		{
			return false;
		}

		return parent::load(array(
			'response_id' => (int) $response_id,
			'ip'          => (string) $ip
		));
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		$this->response_id = intval($this->response_id);
		if (!$this->response_id)
		{
			$this->setError(Lang::txt('Missing response ID'));
		}

		$this->helpful = strtolower(trim($this->helpful));
		if (!$this->helpful)
		{
			$this->setError(Lang::txt('Missing vote'));
		}

		if (!in_array($this->helpful, array(1, 'yes', 'like', 'up', -1, 'no', 'dislike', 'down')))
		{
			$this->setError(Lang::txt('Invalid vote'));
		}

		if (!Validate::ip($this->ip))
		{
			$this->setError(Lang::txt('Invalid IP address'));
		}

		if ($this->getError())
		{
			return false;
		}

		return true;
	}

	/**
	 * Check if a vote has been registered for an answer/IP
	 *
	 * @param   integer  $response_id  Answer ID
	 * @param   string   $ip           IP address
	 * @return  integer
	 */
	public function checkVote($response_id=null, $ip=null)
	{
		$response_id = $response_id ?: $this->response_id;

		if ($response_id == null)
		{
			return 0;
		}

		$query = "SELECT helpful FROM `$this->_tbl` WHERE response_id=" . $this->_db->quote($response_id) . " AND ip=" . $this->_db->quote($ip);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Delete a record by answer ID
	 *
	 * @param   integer  $response_id  Answer ID
	 * @return  boolean  True on success, false if error
	 */
	public function deleteLog($response_id=null)
	{
		$response_id = $response_id ?: $this->response_id;

		if ($response_id == null)
		{
			return false;
		}

		$this->_db->setQuery("DELETE FROM $this->_tbl WHERE response_id=" . $this->_db->quote($response_id));
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}
}

