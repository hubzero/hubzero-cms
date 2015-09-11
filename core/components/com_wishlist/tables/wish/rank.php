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

namespace Components\Wishlist\Tables\Wish;

use Lang;

/**
 * Table class for wish ranking
 */
class Rank extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__wishlist_vote', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		if (trim($this->wishid) == '')
		{
			$this->setError(Lang::txt('WISHLIST_ERROR_NO_WISHID'));
			return false;
		}

		return true;
	}

	/**
	 * Get a record and bind to $this
	 *
	 * @param   integer  $userid  User ID
	 * @param   integer  $wishid  Wish ID
	 * @return  boolean  False if error, True on success
	 */
	public function load_vote($userid=NULL, $wishid=NULL)
	{
		$userid = $userid ?: $this->userid;
		$wishid = $wishid ?: $this->wishid;

		if ($userid === NULL or $wishid === NULL)
		{
			$this->setError(Lang::txt('Missing wish or user ID'));
			return false;
		}

		return parent::load(array(
			'userid' => $userid,
			'wishid' => $wishid
		));
	}

	/**
	 * Get votes on a wish
	 *
	 * @param   integer  $wishid  Wish ID
	 * @return  mixed    False if error, array on success
	 */
	public function get_votes($wishid=NULL)
	{
		$wishid = $wishid ?: $this->wishid;

		if ($wishid === NULL)
		{
			$this->setError(Lang::txt('Missing wish ID'));
			return false;
		}

		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE wishid=" . $this->_db->quote($wishid));
		return $this->_db->loadObjectList();
	}

	/**
	 * Remove a vote
	 *
	 * @param   integer  $wishid  Wish ID
	 * @param   integer  $userid  User ID
	 * @return  boolean  False if error, True on success
	 */
	public function remove_vote($wishid=NULL, $userid=NULL)
	{
		$userid = $userid ?: $this->userid;
		$wishid = $wishid ?: $this->wishid;

		if ($wishid === NULL)
		{
			$this->setError(Lang::txt('Missing wish ID'));
			return false;
		}

		$query = "DELETE FROM $this->_tbl WHERE wishid=" . $this->_db->quote($wishid);
		if ($oid)
		{
			$query .= " AND userid=" . $this->_db->quote($oid);
		}

		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}
}

