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

namespace Components\Poll\Tables;

use Lang;
use Date;

/**
 * Table class for a poll
 */
class Poll extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  $db  JDatabase
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__polls', 'id', $db);
	}

	/**
	 * Binds an array to the object
	 *
	 * @param   array    Named array
	 * @param   string   Space separated list of fields not to bind
	 * @return  boolean
	 */
	public function bind($array, $ignore='')
	{
		$result = parent::bind($array);

		// Cast properties
		$this->id = (int) $this->id;

		return $result;
	}

	/**
	 * Overloaded check function
	 *
	 * @return  boolean
	 */
	public function check()
	{
		// Check for valid name
		$this->title = trim($this->title);
		if ($this->title == '')
		{
			$this->setError(Lang::txt('COM_POLL_MISSING_TITLE'));
			return false;
		}

		// Check for valid lag
		$this->lag = intval($this->lag);
		if ($this->lag == 0)
		{
			$this->setError(Lang::txt('COM_POLL_MISSING_LAG'));
			return false;
		}

		if (empty($this->alias))
		{
			$this->alias = $this->title;
		}
		$this->alias = \JFilterOutput::stringURLSafe($this->alias);
		if (trim(str_replace('-','',$this->alias)) == '')
		{
			$this->alias = Date::of('now')->toFormat("%Y-%m-%d-%H-%M-%S");
		}

		return true;
	}

	/**
	 * Delete a record
	 *
	 * @param   integer  $oid  Record ID
	 * @return  boolean
	 */
	public function delete($oid=null)
	{
		$k = $this->_tbl_key;
		if ($oid)
		{
			$this->$k = intval($oid);
		}

		if (parent::delete($oid))
		{
			$query = 'DELETE FROM `#__poll_data` WHERE pollid = ' . (int) $this->$k;
			$this->_db->setQuery($query);
			if (!$this->_db->query())
			{
				$this->setError($this->_db->getErrorMsg());
			}

			$query = 'DELETE FROM `#__poll_date` WHERE poll_id = ' . (int) $this->$k;
			$this->_db->setQuery($query);
			if (!$this->_db->query())
			{
				$this->setError($this->_db->getErrorMsg());
			}

			$query = 'DELETE FROM `#__poll_menu` WHERE pollid = ' . (int) $this->$k;
			$this->_db->setQuery($query);
			if (!$this->_db->query())
			{
				$this->setError($this->_db->getErrorMsg());
			}

			return true;
		}

		return false;
	}

	/**
	 * Add vote
	 *
	 * @param   integer  $poll_id    The id of the poll
	 * @param   integer  $option_id  The id of the option selected
	 * @return  void
	 */
	public function vote($poll_id, $option_id)
	{
		$poll_id   = (int) $poll_id;
		$option_id = (int) $option_id;

		$query = 'UPDATE `#__poll_data`'
			. ' SET hits = hits + 1'
			. ' WHERE pollid = ' . (int) $poll_id
			. ' AND id = ' . (int) $option_id
			;
		$this->_db->setQuery($query);
		$this->_db->query();

		$query = 'UPDATE `#__polls`'
			. ' SET voters = voters + 1'
			. ' WHERE id = ' . (int) $poll_id
			;
		$this->_db->setQuery($query);
		$this->_db->query();

		$query = 'INSERT INTO `#__poll_date`'
			. ' SET date = ' . $this->_db->quote(Date::toSql())
			. ', vote_id = ' . (int) $option_id
			. ', poll_id = ' . (int) $poll_id
		;
		$this->_db->setQuery($query);
		$this->_db->query();
	}

	/**
	 * Get the latest poll
	 *
	 * @return  object
	 */
	public function getLatest()
	{
		$result = null;

		$query = 'SELECT id'
			.' FROM `#__polls`'
			.' WHERE published = 1 AND open = 1 ORDER BY id DESC Limit 1'
			;
		$this->_db->setQuery($query);
		$result = $this->_db->loadResult();

		if ($this->_db->getErrorNum())
		{
			$this->setError($this->_db->stderr());
			return false;
		}

		$poll = new self($this->_db);
		$poll->load($result);

		return $poll;
	}

	/**
	 * Get options for a poll
	 *
	 * @param   integer  $id    The id of the poll
	 * @return  array
	 */
	public function getPollOptions($id)
	{
		$query = 'SELECT id, text' .
			' FROM `#__poll_data`' .
			' WHERE pollid = ' . (int) $id .
			' AND text <> ""' .
			' ORDER BY id';
		$this->_db->setQuery($query);

		if (!($options = $this->_db->loadObjectList()))
		{
			return array();
		}

		return $options;
	}
}
