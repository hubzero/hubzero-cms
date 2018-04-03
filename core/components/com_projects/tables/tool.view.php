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

namespace Components\Projects\Tables;

use Hubzero\Database\Table;
use Date;

/**
 * Project Tool View class
 */
class ToolView extends Table
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__project_tool_views', 'id', $db);
	}

	/**
	 * Get last view
	 *
	 * @param   integer  $toolid  Project tool id
	 * @param   integer  $userid  User id
	 * @return  mixed    Return string or NULL
	 */
	public function loadView($toolid = 0, $userid = 0)
	{
		if (!intval($toolid) || !intval($userid))
		{
			return false;
		}

		$query  = "SELECT * FROM $this->_tbl
				   WHERE parent_id=" . $this->_db->quote($toolid) . "
				   AND userid=" . $this->_db->quote($userid) . "
				   ORDER BY viewed DESC LIMIT 1";

		$this->_db->setQuery($query);
		if ($result = $this->_db->loadAssoc())
		{
			return $this->bind($result);
		}
		else
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
	}

	/**
	 * Check if page was viewed recently
	 *
	 * @param   integer  $toolid  Project tool id
	 * @param   integer  $userid  User id
	 * @return  mixed    Return string or NULL
	 */
	public function checkView($toolid = 0, $userid = 0)
	{
		if (!intval($toolid) || !intval($userid))
		{
			return false;
		}

		$now      = Date::toSql();
		$lastView = null;

		if ($this->loadView($toolid, $userid))
		{
			$lastView = $this->viewed;
		}
		else
		{
			$this->parent_id = $toolid;
			$this->userid    = $userid;
		}

		// Record new viewing time for future comparison
		$this->viewed = $now;
		$this->store();

		return $lastView;
	}
}
