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
use Date;
use User;

/**
 * Table class for wishlist plan
 */
class Plan extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__wishlist_implementation', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		$this->pagetext = rtrim($this->pagetext);
		if (!$this->pagetext)
		{
			$this->setError(Lang::txt('Please provide a plan'));
		}

		$this->version = intval($this->version);

		$this->wishid = intval($this->wishid);
		if (!$this->wishid)
		{
			$this->setError(Lang::txt('Please provide a wish ID'));
		}

		if ($this->getError())
		{
			return false;
		}

		if (!$this->id)
		{
			$this->created    = Date::toSql();
			$this->created_by = User::get('id');
		}

		return true;
	}

	/**
	 * Get a record for a wish
	 *
	 * @param   integer  $wishid  Wish ID
	 * @return  mixed    False if error, array on success
	 */
	public function getPlan($wishid)
	{
		if ($wishid == null)
		{
			return false;
		}

		$query  = "SELECT *, xp.name AS authorname
					FROM `#__wishlist_implementation` AS p
					LEFT JOIN `#__users` AS xp ON xp.id=p.created_by
					WHERE p.wishid = " . $this->_db->quote($wishid) . " ORDER BY p.created DESC LIMIT 1";
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Delete a record based on wish
	 *
	 * @param   integer  $wishid  Wish ID
	 * @return  boolean  False if errors, True on success
	 */
	public function deletePlan($wishid)
	{
		if ($wishid == null)
		{
			return false;
		}

		$this->_db->setQuery("DELETE FROM $this->_tbl WHERE wishid=" . $this->_db->quote($wishid));
		$this->_db->query();
		return true;
	}
}
