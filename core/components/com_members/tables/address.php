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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Tables;

use Lang;

/**
 * Table class for member addresses
 */
class Address extends \JTable
{
	/**
	 * Object constructor to set table and key field
	 *
	 * @param   object  $db  Database object
	 * @return  void
	 */
	public function __construct($db)
	{
		parent::__construct('#__xprofiles_address', 'id', $db);
	}

	/**
	 * Method for checking that fields are valid before sending to the database
	 *
	 * @return  boolean  True if the object is ok
	 */
	public function check()
	{
		if (!isset($this->uidNumber) || $this->uidNumber == '')
		{
			$this->setError(Lang::txt('You must supply a user id.'));
			return false;
		}

		return true;
	}

	/**
	 * Method to verify we can delete address
	 *
	 * @param   unknown  $pk
	 * @param   unknown  $joins
	 * @return  boolean
	 */
	public function canDelete($pk = NULL, $joins = NULL)
	{
		return true;
	}

	/**
	 * Method to get addressed for member
	 *
	 * @param   integer  $uidNumber  Member User Id
	 * @return  array
	 */
	public function getAddressesForMember($uidNumber)
	{
		// Make sure we have a user id
		if (!isset($uidNumber))
		{
			$this->setError(Lang::txt('You must supply a user id.'));
			return false;
		}

		// Query database for addresses for user id
		$sql = "SELECT * FROM {$this->_tbl} WHERE uidNumber=" . $this->_db->quote($uidNumber);
		$this->_db->setQuery($sql);

		return $this->_db->loadObjectList();
	}
}
