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

namespace Components\Support\Tables;

use Lang;
use User;
use Date;

/**
 * Table class for support ticket comment
 */
class Comment extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__support_comments', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		$this->comment = trim($this->comment);
		if (!$this->comment && trim($this->changelog) == '')
		{
			$this->setError(Lang::txt('COM_SUPPORT_ERROR_BLANK_COMMENT'));
		}

		$this->ticket = intval($this->ticket);
		if (!$this->ticket)
		{
			$this->setError(Lang::txt('COM_SUPPORT_ERROR_BLANK_TICKET'));
		}

		if ($this->getError())
		{
			return false;
		}

		if (!$this->created_by)
		{
			$this->created_by = User::get('id');
		}

		if ($this->created_by && is_string($this->created_by))
		{
			$owner = User::getInstance($this->created_by);
			if ($owner && $owner->get('id'))
			{
				$this->created_by = (int) $owner->get('id');
			}
		}

		if (!$this->created)
		{
			$this->created = Date::toSql();
		}

		return true;
	}

	/**
	 * Get comments on a ticket
	 *
	 * @param   integer  $authorized  Administrator access?
	 * @param   integer  $ticket      Ticket ID
	 * @param   string   $sort        Field to sort by
	 * @param   string   $dir         Direction to sort
	 * @return  array
	 */
	public function getComments($authorized, $ticket=NULL, $sort='id', $dir='ASC')
	{
		if (!$ticket)
		{
			$ticket = $this->id;
		}
		if ($authorized)
		{
			$sqladmin = "";
		}
		else
		{
			$sqladmin = "AND access=0";
		}
		$dir = strtoupper($dir);
		if (!in_array($dir, array('ASC', 'DESC')))
		{
			$dir = 'ASC';
		}
		$sql = "SELECT * FROM $this->_tbl WHERE ticket=" . $this->_db->quote($ticket) . " $sqladmin ORDER BY " . $sort . " " . $dir;

		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get a count of comments on a ticket
	 *
	 * @param   integer  $authorized  Administrator access?
	 * @param   integer  $ticket      Ticket ID
	 * @return  integer
	 */
	public function countComments($authorized, $ticket=NULL)
	{
		if (!$ticket)
		{
			$ticket = $this->_ticket;
		}
		if ($authorized)
		{
			$sqladmin = "";
		}
		else
		{
			$sqladmin = "AND access=0";
		}
		$this->_db->setQuery("SELECT COUNT(*) FROM $this->_tbl WHERE ticket=" . $this->_db->quote($ticket) . " $sqladmin");
		return $this->_db->loadResult();
	}

	/**
	 * Get the newest comment on a ticket
	 *
	 * @param   integer  $authorized  Administrator access?
	 * @param   integer  $ticket      Ticket ID
	 * @return  object
	 */
	public function newestComment($authorized, $ticket=NULL)
	{
		if (!$ticket)
		{
			$ticket = $this->_ticket;
		}
		if ($authorized)
		{
			$sqladmin = "";
		}
		else
		{
			$sqladmin = "AND access=0";
		}
		$this->_db->setQuery("SELECT created FROM $this->_tbl WHERE ticket=" . $this->_db->quote($ticket) . " $sqladmin ORDER BY created DESC LIMIT 1");
		return $this->_db->loadResult();
	}

	/**
	 * Get the newest comment on a ticket
	 *
	 * @param   integer  $authorized  Administrator access?
	 * @param   integer  $ticket      Ticket ID
	 * @return  object
	 */
	public function newestCommentsForTickets($authorized, $ticket=NULL)
	{
		if (!$ticket)
		{
			$ticket = $this->_ticket;
		}
		if (is_array($ticket))
		{
			$ticket = array_map('intval', $ticket);
			$ticket = implode(',', $ticket);
		}
		if ($authorized)
		{
			$sqladmin = "";
		}
		else
		{
			$sqladmin = "AND access=0";
		}
		$this->_db->setQuery("SELECT ticket, MAX(created) AS lastactivity FROM $this->_tbl WHERE ticket IN (" . $ticket . ") $sqladmin GROUP BY ticket");
		return $this->_db->loadAssocList('ticket');
	}

	/**
	 * Delete comments based on parent ticket ID
	 *
	 * @param   integer  $ticket  Ticket ID
	 * @return  boolean  True on success
	 */
	public function deleteComments($ticket=NULL)
	{
		if ($ticket === NULL)
		{
			$ticket = $this->ticket;
		}
		$this->_db->setQuery("DELETE FROM $this->_tbl WHERE ticket=" . $this->_db->quote($ticket));
		if (!$this->_db->query())
		{
			$this->setError($database->getErrorMsg());
			return false;
		}
	}
}

