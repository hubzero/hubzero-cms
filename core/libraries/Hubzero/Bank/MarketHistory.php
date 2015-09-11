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

namespace Hubzero\Bank;

/**
 * Market History class:
 * Logs batch transactions, royalty distributions and other big transactions
 */
class MarketHistory extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__market_history', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		$this->itemid = intval($this->itemid);
		if (!$this->itemid)
		{
			$this->setError(\Lang::txt('Entry must have an item ID.'));
		}

		$this->category = trim($this->category);
		if (!$this->category)
		{
			$this->setError(\Lang::txt('Entry must have a category.'));
		}

		if ($this->getError())
		{
			return false;
		}

		if (!$this->date)
		{
			$this->date = \Date::toSql();
		}

		return true;
	}

	/**
	 * Get the ID of a record matching the data passed
	 *
	 * @param   mixed    $itemid    Integer
	 * @param   string   $action    Transaction type
	 * @param   string   $category  Transaction category
	 * @param   string   $created   Transaction date
	 * @param   string   $log       Transaction log
	 * @return  integer
	 */
	public function getRecord($itemid=0, $action='', $category='', $created='', $log = '')
	{
		if ($itemid === NULL)
		{
			$itemid = $this->itemid;
		}
		if ($action === NULL)
		{
			$action = $this->action;
		}
		if ($category === NULL)
		{
			$category = $this->category;
		}

		$sql = "SELECT id FROM $this->_tbl";

		$where = array();
		if ($itemid)
		{
			$where[] = "itemid=" . $this->_db->quote($itemid);
		}
		if ($action)
		{
			$where[] = "action=" . $this->_db->quote($action);
		}
		if ($category)
		{
			$where[] = "category=" . $this->_db->quote($category);
		}
		if ($created)
		{
			$where[] = "`date` LIKE '" . $created . "%'";
		}
		if ($log)
		{
			$where[] = "log=" . $this->_db->quote($log);
		}
		if (count($where) > 0)
		{
			$sql .= " WHERE " . implode(" AND ", $where);
		}

		$sql .= " LIMIT 1";

		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}
}

